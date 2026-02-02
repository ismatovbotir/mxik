<?php

use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProxyController;


Route::resource('/product', ProductController::class);

Route::any('/proxy/{path?}', [ProxyController::class, 'handle'])
  ->where('path', '.*');
