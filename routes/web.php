<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KapsuleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersBannedKapsuleController;
use Illuminate\Support\Facades\Route;

// Redirection publique
Route::get('/', function () {
    return redirect()->route('login');
});

// routes qui nécessitent d'être connecté
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::post('/kapsules', [KapsuleController::class, 'store'])->name('kapsules.store');
    Route::post('/media/upload', [MediaController::class, 'store'])->name('media.upload');

    Route::post('/kapsules/{kapsule}/join', [KapsuleController::class, 'join'])->name('kapsules.join');
    Route::post('/kapsules/{kapsule}/leave', [KapsuleController::class, 'leave'])->name('kapsules.leave');

    // Middleware pour vérifier que l'utilisateur a accès à la kapsule pour les routes suivantes
    Route::middleware(\App\Http\Middleware\CheckKapsuleAccess::class)->group(function () {
        Route::get('/kapsule/{kapsule}', [KapsuleController::class, 'load'])->name('kapsule.load');
        Route::get('/kapsules/{kapsule}/download', [KapsuleController::class, 'downloadZip'])->name('kapsules.downloadZip');
    });

    // Middleware pour vérifier que l'utilisateur est le propriétaire de la kapsule pour les routes suivantes
    Route::middleware(\App\Http\Middleware\CheckIfOwnerKapsule::class)->group(function () {
        Route::get('/kapsules/{kapsule}/edit', [KapsuleController::class, 'edit'])->name('kapsules.edit');
        Route::post('/kapsules/{kapsule}/modify', [KapsuleController::class, 'modify'])->name('kapsules.modify');
        Route::delete('/kapsules/{kapsule}', [KapsuleController::class, 'destroy'])->name('kapsules.destroy');

        Route::post('/kapsules/{kapsule}/ban/{member}', [KapsuleController::class, 'ban'])->name('kapsules.ban');
        Route::get('/kapsules/{kapsule}/banned-users', [UsersBannedKapsuleController::class, 'index'])->name('kapsules.banned-users');
        Route::post('/kapsules/{kapsule}/unban/{member}', [UsersBannedKapsuleController::class, 'unban'])->name('kapsules.unban');
        Route::post('/kapsules/{kapsule}/accept/{member}', [KapsuleController::class, 'accept'])->name('kapsules.accept');
        Route::post('/kapsules/{kapsule}/reject/{member}', [KapsuleController::class, 'reject'])->name('kapsules.reject');
    });

    Route::middleware(\App\Http\Middleware\CheckIfMediaIsFromOwnerOrImporter::class)->group(function () {
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });
});

require __DIR__.'/auth.php';
