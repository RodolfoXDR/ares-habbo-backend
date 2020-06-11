<?php

/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE.md (GNU License)
 */

use Slim\App;

return function (App $app) {
    $app->get('/', 'App\Controller\Status\StatusController:getStatus');

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->get('/users', 'App\Controller\User\UserController:all');
    $app->get('/user', 'App\Controller\User\UserController:user')->add(\App\Middleware\AuthMiddleware::class);

    $app->post('/login', 'App\Controller\Auth\AuthController:login');
    $app->post('/register', 'App\Controller\Auth\AuthController:register');
};
