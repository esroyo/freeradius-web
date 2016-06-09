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
    'prefix' => 'api/v1',
    'namespace' => 'API',
    'middleware' => ['api']
], function () {

    Route::group([
        'prefix' => 'reports'
    ], function () {
        Route::get('/radacct', 'RadacctReportController@show');

    });

});

// web
Route::group([
    'middleware' => ['csrf'] // 'web' implicit
], function () {


    // Authentication
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::post('login', 'Auth\AuthController@postLogin');
    Route::get('logout', 'Auth\AuthController@logout');

    // Registration
    // Route::get('register', 'Auth\AuthController@getRegister');
    // Route::post('register', 'Auth\AuthContoller@postRegister');

    Route::get('/', function () {
        return view('welcome');
    })->middleware(['auth']);

});
