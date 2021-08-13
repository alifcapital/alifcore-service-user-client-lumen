<?php

namespace AlifCapital\UserServiceClient;

use Illuminate\Support\ServiceProvider as BaseProvider;
use AlifCapital\UserServiceClient\Console\PublishConfigCommand;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
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
