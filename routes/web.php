<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::resource('categories', CategoryController::class);
