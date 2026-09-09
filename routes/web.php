<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks.index');
    Route::post('/tasks', 'store')->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', 'toggle')->name('tasks.toggle');
    Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');
});
