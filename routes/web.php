<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');