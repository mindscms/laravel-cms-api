<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\ApiController;

Route::get('/chart/comments_chart',     [ApiController::class, 'comments_chart']);
Route::get('/chart/users_chart',        [ApiController::class, 'users_chart']);
