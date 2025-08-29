<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::resource('/', MainController::class);
Route::resource('/product',ProductController)
