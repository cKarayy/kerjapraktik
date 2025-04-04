<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\LaporanKehadiranController;

Route::post('/logout', function () {
    //Auth::logout();
    return redirect('/admin.login');
})->name('logout');

//admin
Route::get('/', function () {
    return view('admin.welcome');
});

Route::get('/admin/register', [AdminController::class, 'showRegister'])->name('admin.register');
Route::post('/admin/register', [AdminController::class, 'registerAdmin'])->name('admin.register.submit');

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth:admin');

Route::get('/qrcode', function () {
    return view('admin.qr');
})->name('qrcode');

Route::post('/scan-qr', [QRController::class, 'scanQR'])->name('scan.qr');

Route::get('/admin/laporan', [LaporanKehadiranController::class, 'getReport'])->name('admin.laporan');

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

