<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\LaporanKehadiranController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Models\Employee;

//admin
Route::get('/', function () {
    return view('admin.welcome');
});

Route::get('/admin/register', [AdminController::class, 'showRegister'])->name('admin.register');
Route::post('/admin/register', [AdminController::class, 'registerAdmin'])->name('admin.register.submit');

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');

Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'admDashboard'])->name('admin.dashboard');
    Route::get('/penyelia/dashboard', [AdminController::class, 'penyeliaDashboard'])->name('dashboard_py');
});

// Default login route (fallback jika auth gagal)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/qrcode', function () {
    return view('admin.qr');
})->name('qrcode');

//Route::get('/generate-qr', [QRController::class, 'generate'])->name('qrcode');
Route::post('/scan-qr', [QRController::class, 'scanQR'])->name('scan.qr');

Route::get('/generate-qr/{shift}', [QRController::class, 'generate'])->name('generate.qr.shift');

Route::get('/admin/laporan', [LaporanKehadiranController::class, 'getReport'])->name('admin.laporan');

Route::get('/data_admin', function () {
    $employees = Employee::all()->map(function ($employee) {
        return [
            'id' => $employee->id_karyawan,
            'name' => $employee->nama_lengkap,
            'role' => $employee->jabatan,
            'status' => $employee->status,
            'shift' => $employee->shift->nama_shift ?? '-',
            'photo' => $employee->photo ?? null,
        ];
    });

    return view('admin.datapegawai', compact('employees'));
})->name('data_admin');

Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/admin/employees/store', [EmployeeController::class, 'store'])->name('employees.store');


//Penyelia
Route::get('/dashboard_py', function () {
    return view('penyelia.db');
})->name('dashboard_py');

Route::get('/data_py', function () {
    $employees = Employee::with('shift')->get()->map(function ($employee) {
        return [
            'id' => $employee->id_karyawan,
            'name' => $employee->nama_lengkap,
            'role' => $employee->jabatan,
            'status' => $employee->status,
            'shift' => $employee->shift->nama_shift ?? '-',
            'photo' => $employee->photo ?? null,
        ];
    });

    return view('penyelia.dataPenyelia', compact('employees'));
})->name('data_py');


Route::post('/data_py/add', [EmployeeController::class, 'add'])->name('data_py.add');
Route::post('/data_py/delete', [EmployeeController::class, 'destroy'])->name('data_py.delete');
Route::post('/data_py/edit', [EmployeeController::class, 'update'])->name(name: 'data_py.edit');
Route::post('/data-py/save-all', [EmployeeController::class, 'saveAll'])->name('data_py.saveAll');

Route::get('/shifts/all', [ShiftController::class, 'getAll']);
Route::post('/shifts/tambah', [ShiftController::class, 'store']);
Route::post('/shifts/update-multiple', [ShiftController::class, 'updateMultiple']);
Route::post('/shifts/delete-multiple', [ShiftController::class, 'deleteMultiple']);

//pegawai 
Route::get('/pegawai/registerPg', [UserController::class, 'showRegister'])->name('pegawai.registerPg');
Route::post('/pegawai/registerPg', [UserController::class, 'registerPegawai'])->name('pegawai.registerPg.submit');

Route::get('/pegawai/login', [LoginUserController::class, 'showLoginForm'])->name('pegawai.login');
Route::post('/pegawai/login', [LoginUserController::class, 'login'])->name('pegawai.login.submit');

Route::post('/admin/logout', [LoginUserController::class, 'logout'])->name('pegawai.logout');

// Route::get('/pegawai/register', function () {
//     return view('pegawai.registerPg');
// })->name('pegawai.register');

// Route::get('/pegawai/login', function () {
//     return view('pegawai.loginPg');
// })->name('pegawai.login');

Route::get('/pegawai/home', action: function () {
    return view('pegawai.home');
})->name('pegawai.home');

Route::get('/pegawai/history', action: function () {
    return view('pegawai.history');
})->name('pegawai.history');

