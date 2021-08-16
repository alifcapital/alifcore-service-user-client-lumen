User Service lumen client Package
==========

User Client 0.1 for Lumen 8

Installation
============

add this lines to composer.json file:
```php
composer require alifcapital/alifcore-service-user-client-lumen   
```

add this line in Register Service Providers section (bootstrap/app.php):
```php
$app->register(AlifCapital\UserServiceClient\ServiceProvider::class);
```

add routeMiddleware (bootstrap\app.php):
```php
 $app->routeMiddleware([
     'auth' => App\Http\Middleware\Authenticate::class,
     'role' => AlifCapital\UserServiceClient\Http\Middleware\RoleMiddleware::class
 ]);
```


Configuration
============
- Run `php artisan user_client:publish-config` to publish configs (`config/user_client.php`)

add this line in Register configure section (bootstrap/app.php):
```php
$app->configure('user_client');   
```

add this line in Environments (.env):
```dotenv
USER_CLIENT_SERVICE_NAME=alif-shop-settings #(every service had unique service_name)
USER_SERVICE_BASE_URL={url}/service_user #(URL of user service)
USER_CLIENT_PUBLIC_KEY_TTL=60 #CACHE IN SECOUNDS 
```

migrate:
```php
    php artisan migrate
```
