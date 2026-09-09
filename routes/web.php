<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\UserController;
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
    Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);

    // Admin Group: Role Middleware Protected
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

// To-Do Lists (dari branch main)
Route::controller(TodoListController::class)->group(function () {
    Route::get('/lists', 'index')->name('lists.index');
    Route::post('/lists', 'store')->name('lists.store');
    Route::delete('/lists/{todoList}', 'destroy')->name('lists.destroy');
    Route::post('/lists/{todoList}/members', 'addMember')->name('lists.add-member');
});

// Tasks (dari branch main)
Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks.index');
    Route::post('/tasks', 'store')->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', 'toggle')->name('tasks.toggle');
    Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');
});
