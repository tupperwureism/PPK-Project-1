<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.users.index')
            : redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Guest Routes: Login
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Authenticated Routes
Route::middleware('auth')->group(function (): void {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User Dashboard (mocking data)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Programmer 1: Route User Management (users.blade.php)
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'store', 'destroy']);

    // Admin Group: Role Middleware Protected
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
