<?php

use Illuminate\Support\Facades\Route;
use Pp\Creator\Http\Controllers\MenuController;

Route::resources([
    'menu' => MenuController::class,
]);
