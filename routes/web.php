<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);
