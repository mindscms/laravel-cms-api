<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\ApiController;
use App\Http\Controllers\Api\General\GeneralController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Users\UsersController;


Route::get('/chart/comments_chart',             [ApiController::class, 'comments_chart']);
Route::get('/chart/users_chart',                [ApiController::class, 'users_chart']);


/***** API ************************************/
Route::get('/all_posts',                        [GeneralController::class, 'get_posts']);
Route::get('/post/{slug}',                      [GeneralController::class, 'show_post']);


Route::post('register',                         [AuthController::class, 'register']);
Route::post('login',                            [AuthController::class, 'login']);
Route::post('refresh_token',                    [AuthController::class, 'refresh_token']);


Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/my_posts',                     [UsersController::class, 'my_posts']);
    Route::get('/my_posts/create',              [UsersController::class, 'create_post']);
    Route::post('/my_posts/create',             [UsersController::class, 'store_post']);
    Route::get('/my_posts/{post}/edit',         [UsersController::class, 'edit_post']);
    Route::patch('/my_posts/{post}/edit',       [UsersController::class, 'update_post']);
    Route::delete('/my_posts/{post}',           [UsersController::class, 'delete_post']);


    Route::post('logout',                       [UsersController::class, 'logout']);
});





