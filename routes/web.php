<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::name('admin.')->group(function () {
            Route::prefix('clients')->name('clients.')->group(function () {
                Route::any('/', 'ClientsController@index')->name('index');
                Route::any('/export', 'ClientsController@export')->name('export');
                Route::any('client/{id?}', 'ClientsController@client')->name('client');
                Route::any('form/{id?}', 'ClientsController@form')->name('form');
                Route::any('leads/{id?}', 'ClientsController@leads')->name('leads');
                Route::post('formProcessing', 'ClientsController@formProcessing')->name('formProcessing');
                Route::post('formUpdate', 'ClientsController@formUpdate')->name('formUpdate');
            });
            Route::any('/', 'LaravueController@index');
            Route::any('/{path}', 'LaravueController@index');
        });
    });
});
