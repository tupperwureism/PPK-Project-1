<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(TodoListController::class)->group(function () {
    Route::get('/lists', 'index')->name('lists.index');
    Route::post('/lists', 'store')->name('lists.store');
    Route::delete('/lists/{todoList}', 'destroy')->name('lists.destroy');
    Route::post('/lists/{todoList}/members', 'addMember')->name('lists.add-member');
});

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks.index');
    Route::post('/tasks', 'store')->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', 'toggle')->name('tasks.toggle');
    Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');
});

