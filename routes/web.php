<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KapsuleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersBannedKapsuleController;
use Illuminate\Support\Facades\Route;

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
    Route::middleware('App\Http\Middleware\CheckKapsuleAccess')->group(function () {
        Route::get('/kapsule/{kapsule}', [KapsuleController::class, 'load'])
            ->name('kapsule.load');
    });
    Route::post('/media/upload', [MediaController::class, 'store'])->name('media.upload');
    Route::post('/kapsules/{kapsule}/join', [KapsuleController::class, 'join'])->name('kapsules.join');
    Route::post('/kapsules/{kapsule}/accept/{member}', [KapsuleController::class, 'accept'])->name('kapsules.accept');
    Route::post('/kapsules/{kapsule}/reject/{member}', [KapsuleController::class, 'reject'])->name('kapsules.reject');
    Route::middleware('App\Http\Middleware\CheckIfOwnerKapsule')->group(function () {
        Route::post('/kapsules/{kapsule}/ban/{member}', [KapsuleController::class, 'ban'])->name('kapsules.ban');
        Route::get('/kapsules/{kapsule}/banned-users', [UsersBannedKapsuleController::class, 'index'])->name('kapsules.banned-users');
        Route::post('/kapsules/{kapsule}/unban/{member}', [UsersBannedKapsuleController::class, 'unban'])->name('kapsules.unban');
    });
});

require __DIR__.'/auth.php';
