<?php namespace AlifCapital\UserServiceClient;


use AlifCapital\UserServiceClient\Models\UserClientPublicKey;

use Exception;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\AuthenticationException;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;


class VerifyJwt
{
    /**
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public static function getPublicKey(): ?string
    {
        $publicKeyUrl = config('user_client.user_service_url') . '/auth/public_key';
        try {
            $client = new Client(['timeout' => 2]);
            $response = $client->request('POST', $publicKeyUrl, [
                'json' => [
                    'service_name' => config('user_client.service_name')
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error($e);
            throw (new AuthenticationException($e->getMessage()));
        }

        if ($response->getStatusCode() === Response::HTTP_OK){

            $response = $response->getBody()->getContents();
            $decodeToObject = Utils::jsonDecode($response, false);
            $publicKey = $decodeToObject->response->public_key;

            static::saveToDB($publicKey);

            return $publicKey;
        }

        return null;
    }

    /**
     * @return mixed
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public static function cachedPublicKey(): string
    {
        $cacheKey = 'user_client_cached_public_key';
        $cacheTTl = config('user_client.user_service_url');

        if ($cache = Cache::get($cacheKey)) {
            return $cache;
        }

        $publicKey = UserClientPublicKey::select('public_key')
            ->where('status', UserClientPublicKey::STATUS_ACTIVE)->first();

        if (! is_null($publicKey)){
            $publicKey = $publicKey->public_key;
        }else{
            $publicKey = static::getPublicKey();
        }

        return Cache::remember($cacheKey, $cacheTTl, function () use ($publicKey) {
            return $publicKey;
        });
    }

    /**
     * @param string $publicKey
     */
    public static function saveToDB(string $publicKey): void
    {
        $userClientPublicKey = new UserClientPublicKey();
        $userClientPublicKey->where('status', UserClientPublicKey::STATUS_ACTIVE)->update([
           'status' => UserClientPublicKey::STATUS_INACTIVE
        ]);

        $userClientPublicKey->public_key = $publicKey;
        $userClientPublicKey->status = UserClientPublicKey::STATUS_ACTIVE;
        $userClientPublicKey->save();
    }


    /**
     * @param $jwt
     * @return array|null
     *
     * @throws GuzzleException|AuthenticationException
     */
    public static function verifyToken($jwt): ?array
    {
        $pub = static::cachedPublicKey();
        $publicKey = new Key($pub);
        $getServiceName = config('user_client.service_name');

        $signer = new Sha256();
        try {
            $token = (new Parser())->parse($jwt);
            $appRoles = (array) $token->getClaim('roles');
        }catch (Exception $e) {
            throw (new AuthenticationException($e->getMessage()));
        }

        $serviceExists = array_key_exists($getServiceName, $appRoles);
        $verify = $token->verify($signer, $publicKey) && (! $token->isExpired());

        if ($serviceExists && $verify) {
            return [
                'id' => $token->getClaim('sub'),
                'username' => $token->getClaim('username'),
                'roles' => $appRoles[$getServiceName]
            ];
        }

        return null;
    }

}
