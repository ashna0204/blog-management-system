<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (){

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('blogs')->controller(BlogController::class)->group(function (){

         Route::post('/store','store'); // BLOG-CREATE-API
         Route::get('/index','index'); // BLOG-LIST-API
         Route::get('/show/{blog}', 'show'); // Get single blog
         Route::patch('/update/{blog}','update'); // BLOG-EDIT-API
         Route::delete('/delete/{blog}','destroy'); // BLOG-DELETE-API
         Route::post('/liketoggle/{blog}/like-toggle', 'likeToggle'); // BLOG-LIKE-TOGGLE
   
    });
   
});