<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\ApiController;
use App\Http\Controllers\Api\General\GeneralController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\Auth\LoginController;

Route::get('/chart/comments_chart',     [ApiController::class, 'comments_chart']);
Route::get('/chart/users_chart',        [ApiController::class, 'users_chart']);


/***** API ************************************/
Route::get('/all_posts',                [GeneralController::class, 'get_posts']);
Route::get('/post/{slug}',              [GeneralController::class, 'show_post']);

Route::post('register',                 [RegisterController::class, 'register']);
Route::post('login',                    [LoginController::class, 'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


