<?php

use App\Http\Controllers\LogApiController;
use Illuminate\Support\Facades\Route;

Route::post('/logs', [LogApiController::class, 'store']);
