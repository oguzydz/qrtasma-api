<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AdminController;

// php -S localhost:8000 -t public

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => ['cors']], function() use ($router){

    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/register', 'AuthController@register');
    $router->post('/checkToken', 'AuthController@checkToken');
    $router->post('/checkInput', 'AuthController@checkInput');

    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('/dashboard/{user_id}', 'UserController@dashboard');
        $router->get('/detail/{user_id}', 'UserController@detail');
        $router->get('/animals/{user_id}', 'UserController@animals');
        $router->post('/password/{user_id}', 'UserController@password');
        $router->post('/edit/{user_id}', 'UserController@edit');
    });

    $router->group(['prefix' => 'animal'], function () use ($router) {
        $router->post('/insert', 'AnimalController@insert');
        $router->post('/edit/{animal_id}', 'AnimalController@edit');
        $router->get('/detail/{animal_code}', 'AnimalController@detail');
    });

    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->get('/animals', 'AdminController@animals');
        $router->get('/users', 'AdminController@users');
        $router->get('/owners', 'AdminController@owners');
        $router->get('/orders', 'AdminController@orders');
        $router->get('/dash', 'AdminController@dash');

        $router->get('/animals/print/{animal_code}', 'AdminController@print_animal');
        $router->get('/animals/dash/{animal_code}', 'AdminController@dash');
    });

});

/*
    Ödeme yapıldı mı
    Kayıp kısmını ekle
    Analytics +1 görüntülenme
*/
