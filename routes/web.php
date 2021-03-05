<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
// Route::get('/', 'PagesController@test');
Route::auth();
Route::get('/logout', 'Auth\LoginController@logout');
Route::group(['middleware' => ['auth']], function () {
    /**
     * Main
     */
    Route::get('/', 'PagesController@dashboard');
    Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');

    Route::get('/vision-history', 'HistoryController@visionHistory');
    /**
     * Users
     */
    Route::group(['prefix' => 'users'], function () {
        Route::get('/data', 'UsersController@anyData')->name('users.data');
        Route::get('/users', 'UsersController@users')->name('users.users');
    });
    Route::group(['prefix' => 'document'], function () {
        Route::get('/', 'DocumentController@index')->name('doc.index');
        Route::post('names', 'DocumentController@celebNames')->name('doc.celeb.names');
    });
    /**
     * OpenCV
     */
    Route::group(['prefix' => 'cv'], function () {
        Route::get('/', 'CVController@index')->name('cv.index');
        Route::post('/store', 'CVController@store')->name('cv.store');
        Route::get('/photos', 'CVController@photos')->name('cv.photo');
        Route::post('/download-photos', 'CVController@googlePhotos')->name('cv.google.photo');
        //Test recognition
        Route::post('/test', 'CVController@test')->name('cv.test');
    });
    /**
     * Message
     */
    Route::group(['prefix' => 'message'], function () {
        Route::get('/', 'MessageController@index')->name('msg.index');
        Route::post('/send', 'MessageController@send')->name('msg.send');
    });
    Route::resource('users', 'UsersController');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dropbox-token', 'CallbackController@dropbox')->name('dropbox.callback');
    Route::get('/googledrive-token', 'CallbackController@googleDrive')->name('googleDrive.callback');
});
