<?php

use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('lists.index');
});

Route::controller(TodoListController::class)->group(function () {
    Route::get('/lists', 'index')->name('lists.index');
    Route::post('/lists', 'store')->name('lists.store');
    Route::delete('/lists/{todoList}', 'destroy')->name('lists.destroy');
    Route::post('/lists/{todoList}/members', 'addMember')->name('lists.add-member');
});
