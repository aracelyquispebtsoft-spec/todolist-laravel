<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks')->name('home');

Route::resource('tasks', TaskController::class);
Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);
