<?php

use Illuminate\Support\Facades\Route;

Route::get('/{id}', function ($id) {
    return view('welcome', ['id' => $id]);
});