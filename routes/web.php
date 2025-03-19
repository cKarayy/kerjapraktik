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

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('home');

Route::get('/qrcode', function () {
    return view('admin.qr');
})->name('qrcode');

Route::get('/laporan', function () {
    return view('admin.laporan');
})->name('laporan');

Route::get('/data', function () {
    return view('admin.data');
})->name('data');