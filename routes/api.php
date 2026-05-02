<?php

use App\Http\Controllers\Api\ClassCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/class-codes', [ClassCodeController::class, 'index']);
Route::get('/class-codes/{mxik}', [ClassCodeController::class, 'show']);
