<?php namespace AlifCapital\UserServiceClient;

use AlifCapital\UserServiceClient\Auth\UserGeneric;
use Illuminate\Support\ServiceProvider as BaseProvider;
use AlifCapital\UserServiceClient\Console\PublishConfigCommand;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            $jwt = $request->bearerToken();
            if($jwt && $verify = VerifyJwt::verifyToken($jwt)){
                return new UserGeneric([
                    'id' => $verify['id'],
                    'username' => $verify['username'],
                    'roles' => $verify['roles']
                ]);
            }
            return null;
        });

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/user_client.php';
        $this->mergeConfigFrom($configPath, 'user_client');

        $this->app->singleton('command.user_client.publish-config', function () {
            return new PublishConfigCommand();
        });

        $this->commands(
            'command.user_client.publish-config',
        );

    }
}
