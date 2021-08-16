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


Configuration
============
- Run `php artisan user_client:publish-config` to publish configs (`config/user_client.php`)

add this line in Register Service Providers section (bootstrap/app.php):
```php
    composer require alifcapital/alifcore-service-user-client-lumen   
```
