<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/event');
});

Route::get('/event', [EventController::class, 'index'])->name('event');
Route::get('/questions-stats', [EventController::class, 'questionsStats'])->name('questions.stats');
