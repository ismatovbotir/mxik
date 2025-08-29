<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::resource('/', MainController::class);
