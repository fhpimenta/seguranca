<?php

Route::get('/login', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@getLogin')->name('auth.login');
Route::post('/login', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@postLogin')->name('auth.login');
Route::get('/logout', '\Modulos\Seguranca\Http\Controllers\Auth\AuthController@logout')->name('auth.logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', '\Modulos\Seguranca\Http\Controllers\SelecionaModuloController@index')->name('index');

    Route::group(['prefix' => 'seguranca'], function () {

        Route::get('/', '\Modulos\Seguranca\Http\Controllers\DashboardController@index')->name('seguranca.index.index');

        Route::group(['prefix' => 'modulos'], function () {
            Route::get('/', '\Modulos\Seguranca\Http\Controllers\ModulosController@index')->name('seguranca.modulos.index');
        });

        Route::group(['prefix' => 'itens'], function () {
            Route::get('/', '\Modulos\Seguranca\Http\Controllers\ModulosController@index')->name('seguranca.itens.index');
        });
    });
});
