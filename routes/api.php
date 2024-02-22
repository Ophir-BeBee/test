<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::controller(UserController::class)->group(function(){
    Route::post('/register','register');
    Route::post('/login','login');
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[UserController::class,'logout']);

    Route::group(["prefix" => "posts", "controller" => PostController::class], function() {
        Route::post('/','store');
        Route::get('/','index');
        Route::get('/detail/{id}','show');
        Route::get('delete/{id}','destroy');
        Route::post('/update','update');
    });
});
