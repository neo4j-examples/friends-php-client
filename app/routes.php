<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Controllers\FriendsController;
use App\Application\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->get('/users', [UserController::class, 'listUsers']);
    $app->get('/user', [UserController::class, 'getUser']);
    $app->post('/user', [UserController::class, 'createUser']);
    $app->delete('/user', [UserController::class, 'deleteUser']);

    $app->get('/friends/distance', [FriendsController::class, 'distance']);
    $app->get('/user/friends', [FriendsController::class, 'listFriends']);
    $app->put('/user/friend', [FriendsController::class, 'makeFriends']);
    $app->delete('/user/friend', [FriendsController::class, 'breakupFriends']);

};
