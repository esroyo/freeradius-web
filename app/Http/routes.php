<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


// api
Route::group([
    'prefix' => 'api',
    'middleware' => ['request.accept:application/json'],
], function () {

    Route::post('login', 'Auth\AuthController@login');
    Route::post('signup', 'Auth\AuthController@register');
    Route::get('logout', 'Auth\AuthController@logout');

    Route::group([
        'prefix' => 'v1',
        'namespace' => 'API',
        'middleware' => ['api'],
    ], function () {
        Route::get('/radacct', 'RadacctController@show');
    });

});

// web
Route::group([
    'middleware' => ['web'] // 'web' implicit
], function () {

    // Route::auth();
    // Route::get('/home', 'HomeController@index');
    // Route::get('/', 'HomeController@index');
    // TODO: web shows api doc

});
