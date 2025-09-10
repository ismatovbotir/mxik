<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GroupController;

use Illuminate\Support\Facades\Route;

Route::resource('/', MainController::class);
Route::resource('/product', ProductController::class);
Route::resource('/group',GroupController::class);
