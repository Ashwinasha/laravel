<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});

Route::get('/', function () {
    return view('home');
});
