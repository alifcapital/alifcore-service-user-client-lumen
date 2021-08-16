User Service lumen client Package
==========

User Client 0.1 for Lumen 8

Installation
============

add this lines to composer.json file:
```php
     "repositories": [
        {
            "type":"package",
            "package": {
                "name": "alifcapital/alifcore-service-user-client-lumen",
                "version":"master",
                "source": {
                    "url": "https://github.com/alifcapital/alifcore-service-user-client-lumen.git",
                    "type": "git",
                    "reference":"master"
                }
            }
        }
    ],
    "require": {
        "alifcapital/alifcore-service-user-client-lumen": "master"
    }
```

add this line in Register Service Providers section (bootstrap/app.php):
```php
     $app->register(AlifCapital\UserServiceClient\ServiceProvider::class);
```


$app->register(AlifCapital\UserServiceClient\ServiceProvider::class);
Configuration
============
- Run `php artisan swagger-lume:publish-config` to publish configs (`config/swagger-lume.php`)
