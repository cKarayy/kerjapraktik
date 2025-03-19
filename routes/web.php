<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.welcome');
});

Route::get('/login', function () {
    return view('admin.login');
})->name('login');

Route::get('/register', function () {
    return view('admin.register');
})->name('register');