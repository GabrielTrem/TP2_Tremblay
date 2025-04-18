<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\OneCriticPerFilmMiddleware;
//For test
Route::get('/films', 'App\Http\Controllers\FilmController@index');

Route::middleware('throttle:60,1')->group( function(){ 
    Route::middleware('auth:sanctum')->group( function(){ 
        Route::middleware(AdminMiddleware::class)->group( function(){ 
            Route::post('/films', 'App\Http\Controllers\FilmController@store');
            Route::put('/films/{id}', 'App\Http\Controllers\FilmController@update');
            Route::delete('/films/{id}', 'App\Http\Controllers\FilmController@destroy');
        });
        Route::post('/critics', 'App\Http\Controllers\CriticController@store')->middleware(OneCriticPerFilmMiddleware::class);
        Route::get('/user', 'App\Http\Controllers\UserController@show');
        Route::patch('/user', 'App\Http\Controllers\UserController@update');
    });
    
});

Route::middleware('throttle:5,1')->group( function(){
    Route::post('/signout', 'App\Http\Controllers\AuthController@logout')->middleware('auth:sanctum');
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login'); 
});
