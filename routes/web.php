<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KapsuleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/kapsules', [KapsuleController::class, 'store'])->name('kapsules.store');

Route::middleware('auth')->group(function () {
    Route::get('/kapsule/{kapsule}', [KapsuleController::class, 'load'])
        ->name('kapsule.load');
    Route::post('/media/upload', [MediaController::class , 'store'])->name('media.upload');
    Route::post('/kapsules/{kapsule}/join', [KapsuleController::class , 'join'])->name('kapsules.join');
});

require __DIR__.'/auth.php';
