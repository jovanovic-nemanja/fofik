<?php

use Illuminate\Http\Request;

use App\Http\Controllers\CelebController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\JwtAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('users', ['uses' => 'UserController@index']);
    });
});

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::get('/google', [JwtAuthController::class, 'googleProvider']);
    Route::get('/facebook', [JwtAuthController::class, 'facebookProvider']);
    Route::post('/signin', [JwtAuthController::class, 'login']);
    Route::post('/social', [JwtAuthController::class, 'socialProvider']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [JwtAuthController::class, 'logout']);
        Route::post('/refresh', [JwtAuthController::class, 'refresh']);
    });
});

Route::group([
    'prefix' => 'celebs'
], function ($router) {
    Route::post('/follow', [FavoriteController::class, 'store']);
    Route::post('/unfollow', [FavoriteController::class, 'destroy']);
    Route::get('/follow-list', [FavoriteController::class, 'show']);
    
    Route::post('/search', [CelebController::class, 'search']);
    Route::get('/', [CelebController::class, '']);
});
