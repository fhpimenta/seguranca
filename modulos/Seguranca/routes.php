<?php

Route::get('/login', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@getLogin')->name('auth.login');
Route::post('/login', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@postLogin')->name('auth.login');
Route::get('/logout', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@logout')->name('auth.logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', '\Modulos\Seguranca\Http\Controllers\SelecionaModuloController@index')->name('index');

    Route::group(['prefix' => 'seguranca'], function () {
       Route::get('/', function () {
           return view('Seguranca::dashboard.index');
       })->name('seguranca.index');

        Route::get('/show', function () {
            return "<h1>Pagina show do modulo Seguranca</h1>";
        })->name('seguranca.show');

        Route::get('/modulos/', function () {
            return "<h1>Pagina show do modulo Seguranca</h1>";
        })->name('seguranca.modulos.index');
    });
});