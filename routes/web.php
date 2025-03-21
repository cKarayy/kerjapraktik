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
    $employees = array_map(function ($i) {
        return [
            'id' => $i + 1,
            'name' => $i === 22 ? 'Putri Tapasya' : 'Sony Palton',
            'role' => $i === 22 ? 'Food Runner' : 'Perwakilan Owner',
            'status' => $i === 22 ? 'resign' : 'active',
        ];
    }, range(0, 22));

    return view('admin.data', compact('employees'));
});
