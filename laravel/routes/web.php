<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('log.sensitive')->get('/event', [EventController::class, 'index'])->name('event');

Route::middleware('auth')->group(function () {
    Route::post('/dashboard/submit', [DashboardController::class, 'submit'])->name('dashboard.submit');
    Route::middleware('log.sensitive')->group(function () {
        Route::get('/generator', [GeneratorController::class, 'create'])->name('generator.create');
        Route::post('/generator', [GeneratorController::class, 'store'])->name('generator.store');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
