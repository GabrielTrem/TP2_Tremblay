<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('throttle:5,1')->group( function(){
    Route::middleware('auth:sanctum')->group( function(){
        Route::post('/signout', 'App\Http\Controllers\AuthController@logout');
    });
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login'); 
});
#Route::post('/films', 'App\Http\Controllers\FilmController@store');
#Route::put('/films/{id}', 'App\Http\Controllers\FilmController@update');
#Route::delete('/films/{id}', 'App\Http\Controllers\FilmController@destroy');

#Route::post('/critic', 'App\Http\Controllers\CriticController@store');

#Route::get('/user', 'App\Http\Controllers\UserController@show');
#Route::patch('/user', 'App\Http\Controllers\UserController@update');
