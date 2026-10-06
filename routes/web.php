<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'clientAuthFallback'])->middleware('throttle:5,1');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'clientAuthFallback'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/forgot-password', [AuthController::class, 'clientAuthFallback'])->middleware('throttle:3,1');
    Route::post('/reset-password', [AuthController::class, 'clientAuthFallback'])->middleware('throttle:5,1');
});

Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::get('/email/confirmation', [AuthController::class, 'showEmailConfirmation'])->name('auth.email-confirmation');
Route::post('/auth/supabase/session', [AuthController::class, 'establishSupabaseSession'])
    ->middleware('throttle:10,1')->name('auth.supabase.session');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Portfolio creation and management are available only to signed-in owners.
Route::middleware('auth')->group(function (): void {
    Route::get('/portfolio/create', [PortfolioController::class, 'create'])->name('portfolio.create');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->middleware('throttle:10,1')->name('portfolio.store');
    Route::get('/portfolio/{id}/edit', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::get('/portfolio/{id}/export', [PortfolioController::class, 'export'])->middleware('throttle:10,1')->name('portfolio.export');
    Route::get('/portfolio/{id}/export/html', [PortfolioController::class, 'exportHtml'])->middleware('throttle:5,1')->name('portfolio.export.html');
    Route::put('/portfolio/{id}', [PortfolioController::class, 'update'])->middleware('throttle:10,1')->name('portfolio.update');
    Route::delete('/portfolio/{id}', [PortfolioController::class, 'destroy'])->middleware('throttle:5,1')->name('portfolio.destroy');
    Route::post('/portfolio/{id}/restore', [PortfolioController::class, 'restore'])->middleware('throttle:10,1')->name('portfolio.restore');
    Route::delete('/portfolio/{id}/permanent', [PortfolioController::class, 'permanentlyDelete'])->middleware('throttle:5,1')->name('portfolio.permanent-delete');
    Route::get('/portfolio/{id}/template', [PortfolioController::class, 'selectTemplate'])->name('portfolio.template');
    Route::post('/portfolio/{id}/template', [PortfolioController::class, 'applyTemplate'])->middleware('throttle:10,1')->name('portfolio.applyTemplate');
    Route::get('/manage', [PortfolioController::class, 'manage'])->name('portfolio.manage');
});

// Public, read-only portfolio pages. Keep the single-segment catch-all last.
Route::get('/portfolio/{id}/preview', [PortfolioController::class, 'preview'])->name('portfolio.preview');
Route::get('/portfolio/{id}', [PortfolioController::class, 'show'])->name('portfolio.show');
