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
Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
Route::get('logout', 'Auth\LoginController@logout');
Route::view('/', 'home');
Route::get('shop', 'ShopController@index');
Route::get('shop_alt', 'ShopController@index_alt');
Route::get('shop/{id}', 'ShopController@show');
Route::get('contact-us', 'ContactUsController@show');
Route::post('contact-us', 'ContactUsController@sendEmail');
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function(){
    Route::redirect('/', 'records');
    Route::get('genres/qryGenres', 'Admin\GenreController@qryGenres');
    Route::get('users/qryUsers/{nameemail}/{userVar}/{ascdesc}', 'Admin\UserController@qryUsers');
    Route::resource('records', 'Admin\RecordController');
    Route::resource('genres', 'Admin\GenreController');
    Route::resource('users', 'Admin\UserController');
});
Route::redirect('user', '/user/profile');
Route::middleware(['auth'])->prefix('user')->group(function (){
   Route::get('profile', 'User\ProfileController@edit');
   Route::post('profile', 'User\ProfileController@update');
   Route::get('password', 'User\PasswordController@edit');
   Route::post('password', 'User\PasswordController@update');
});
