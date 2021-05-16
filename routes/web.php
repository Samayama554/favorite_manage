<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/top', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', 'TwitterController@index')->name('root');
Route::get('/top', 'TwitterController@top')->name('top');

//======================================================================
//  Twitterログイン
//======================================================================
Route::get('login/twitter', 'Auth\LoginController@redirectToTwitterProvider')->name('login_twitter');
Route::get('login/twitter/callback', 'Auth\LoginController@handleTwitterProviderCallback');
Route::get('logout/twitter', 'Auth\LoginController@logout')->name('logout_twitter');
Route::post('logout/twitter', 'Auth\LoginController@logout')->name('logout_twitter');
