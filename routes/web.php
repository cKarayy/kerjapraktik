<?php

use Illuminate\Support\Facades\Route;

Route::post('/logout', function () {
    //Auth::logout();
    return redirect('/login');
})->name('logout');

//admin
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

Route::get('/data_admin', function () {
    $employees = array_map(function ($i) {
        return [
            'id' => $i + 1,
            'name' => $i === 22 ? 'Putri Tapasya' : 'Sony Palton',
            'role' => $i === 22 ? 'Food Runner' : 'Perwakilan Owner',
            'status' => $i === 22 ? 'resign' : 'active',
        ];
    }, range(0, 22));

    return view('admin.datapegawai', compact('employees'));
})->name('data_admin');

//Penyelia
Route::get('/dashboard_py', function () {
    return view('penyelia.db');
})->name('dashboard_py');

Route::get('/data_py', function () {
    $employees = array_map(function ($i) {
        return [
            'id' => $i + 1,
            'name' => $i === 22 ? 'Putri Tapasya' : 'Sony Palton',
            'role' => $i === 22 ? 'Food Runner' : 'Perwakilan Owner',
            'status' => $i === 22 ? 'resign' : 'active',
        ];
    }, range(0, 22));

    return view('penyelia.dataPenyelia', compact('employees'));
})->name('data_py');

//pegawai
Route::get('/pegawai/login', function () {
    return view('pegawai.loginPg');
})->name('pegawai.login');

Route::get('/pegawai/home', action: function () {
    return view('pegawai.home');
})->name('pegawai.home');

Route::get('/pegawai/history', action: function () {
    return view('pegawai.history');
})->name('pegawai.history');

