<?php
/** @var \Laravel\Lumen\Routing\Router $router */


$router->get('/testik', function (){
    return config('user_client.service_name');;
});

