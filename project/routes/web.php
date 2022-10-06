<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('test', ['uses' => 'Api\UserController@register']);

$router->group(['prefix' => 'api/v1'], static function () use ($router) {
    $router->group(['prefix' => 'user'], static function () use ($router) {
        $router->post('register', ['uses' => 'Api\RegisterController@register']);
        $router->post('sign-in', ['uses' => 'Api\UserController@signIn']);
        $router->post('forgot-password', ['uses' => 'Api\UserController@forgotPassword']);
        $router->post('recover-password', ['uses' => 'Api\UserController@recoverPassword']);
        $router->get('companies', ['uses' => 'Api\UserController@showCompanies']);
        $router->post('companies', ['uses' => 'Api\UserController@addCompanies']);
    });
});
