<?php namespace AlifCapital\UserServiceClient;


use AlifCapital\UserServiceClient\Models\UserClientPublicKey;
use AlifCapital\UserServiceClient\Validation\Constraint\IsExpired;

use Exception;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\AuthenticationException;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;


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
        $cacheTTl = config('user_client.public_key_ttl');

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

        $key = InMemory::plainText($pub);

        $configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::empty(),
            $key
        );

        $getServiceName = config('user_client.service_name');

        try {
            $token = $configuration->parser()->parse($jwt);
            $appRoles = (array) $token->claims()->get('roles');
        }catch (Exception $e) {
            throw (new AuthenticationException($e->getMessage()));
        }

        $serviceExists = array_key_exists($getServiceName, $appRoles);

        $configuration->setValidationConstraints(
            new SignedWith(new Sha256(), $key),
            new IsExpired()
        );

        if ($configuration->validator()->validate($token, ...$configuration->validationConstraints())) {
            return [
                'id' => $token->claims()->get('sub'),
                'username' => $token->claims()->get('username'),
                'roles' => $appRoles[$getServiceName]
            ];
        }

        return null;
    }

}
