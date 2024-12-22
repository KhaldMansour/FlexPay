<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function ($id) {
    return view('welcome');
});