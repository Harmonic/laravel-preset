<?php

use Inertia\Inertia;

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

// Auth
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Users
    Route::get('users')->name('users')->uses('UsersController@index')->middleware('remember');
    Route::get('users/create')->name('users.create')->uses('UsersController@create');
    Route::post('users')->name('users.store')->uses('UsersController@store');
    Route::get('users/{user}/edit')->name('users.edit')->uses('UsersController@edit');
    Route::put('users/{user}')->name('users.update')->uses('UsersController@update');
    Route::delete('users/{user}')->name('users.destroy')->uses('UsersController@destroy');
    Route::put('users/{user}/restore')->name('users.restore')->uses('UsersController@restore');

    Route::get('/example', function () {
        return Inertia::render('Example/Index');
    })->name('example');
});
