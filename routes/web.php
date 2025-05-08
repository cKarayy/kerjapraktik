<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\IzinController;
use App\Models\Employee;
use App\Http\Controllers\PegawaiHistoryController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenyeliaController;
use App\Models\Absensi;

//admin
Route::get('/', function () {
    return view('admin.welcome');
});

Route::get('/admin/register', [AdminController::class, 'showRegister'])->name('admin.register');
Route::post('/admin/register', [AdminController::class, 'registerAdmin'])->name('admin.register.submit');

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');

Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

Route::get('/admin/laporan', [AdminController::class, 'showLaporan'])->name('admin.laporan');

Route::get('/admin/export/laporan', [AdminController::class, 'export'])->name('admin.export.laporan');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'admDashboard'])->name('admin.dashboard');
    Route::get('/penyelia/dashboard', [AdminController::class, 'penyeliaDashboard'])->name('dashboard_py');
});

// Default login route (fallback jika auth gagal)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

//Route::get('/generate-qr', [QRController::class, 'generate'])->name('qrcode');
// Generate QR Code (Auto Refresh)

Route::get('/qrcode', [QRController::class, 'showQRCode'])->name('qrcode');

// Proses Scan QR Code (API)
Route::post('/scan-qr', [QRController::class, 'scan'])->name('scan.qr');


Route::get('/data_admin', function () {
    $employees = Employee::all()->map(function ($employee) {
        return [
            'id' => $employee->id_karyawan,
            'name' => $employee->nama_lengkap,
            'role' => $employee->jabatan,
            'status' => $employee->status,
            'shift' => $employee->shift->nama_shift ?? '-',
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
        ];
    });

    return view('penyelia.dataPenyelia', compact('employees'));
})->name('data_py');

Route::get('/penyelia/laporan', [PenyeliaController::class, 'showLaporan'])->name('penyelia.laporanPy');

Route::middleware(['auth:penyelia'])->post('/izin/approve/{id}', [IzinController::class, 'approve']);
Route::middleware(['auth:penyelia'])->post('/izin/reject/{id}', [IzinController::class, 'reject']);

Route::prefix('pegawai')->group(function () {
    Route::get('/all', [EmployeeController::class, 'index']); // Menampilkan semua pegawai
    Route::put('/update/{id}', [EmployeeController::class, 'update']); // Mengupdate data pegawai
    Route::post('/update/{id}', [EmployeeController::class, 'update']); // Mengupdate data pegawai
    Route::delete('/delete/{id}', [EmployeeController::class, 'delete']); // Menghapus banyak pegawai
});

Route::prefix('shifts')->group(function () {
    Route::get('/all', [ShiftController::class, 'index']); // Menampilkan semua shift
    Route::put('/update/{id}', [ShiftController::class, 'update']); // Mengupdate data shift
    Route::post('/delete-multiple', [ShiftController::class, 'delete']); // Menghapus banyak shift
});

//pegawai
Route::get('/pegawai/register', function () {
    return view('pegawai.registerPg');
})->name('pegawai.register');

Route::get('/pegawai/login', function () {
    return view('pegawai.loginPg');
})->name('pegawai.loginPg');

Route::get('/register-karyawan', [EmployeeController::class, 'create'])->name('karyawans.create');

Route::post('/register-karyawan', action: [EmployeeController::class, 'store'])->name('karyawans.store');

Route::get('/pegawai/register', [EmployeeController::class, 'showRegister'])->name('pegawai.registerPg');

Route::get('/pegawai/login', [LoginUserController::class, 'showLoginForm'])->name('pegawai.loginPg');
Route::post('/pegawai/login', [LoginUserController::class, 'login'])->name('pegawai.login.submit');

Route::middleware('auth:karyawans')->get('/pegawai/home', [LoginUserController::class, 'home'])->name('pegawai.home');
Route::post('/logout', [LoginUserController::class, 'logout'])->name('pegawai.logout');

Route::get('/pegawai/history', [PegawaiHistoryController::class, 'index'])->middleware('auth:karyawans')->name('pegawai.history');

Route::get('/pegawai/all', [EmployeeController::class, 'show'])->name('pegawai.all');

// Route::middleware(['auth:penyelia'])->group(function () {
//     Route::post('/cuti/approve/{id}', [CutiController::class, 'approve']);
//     Route::post('/izin/approve/{id}', [IzinController::class, 'approve']);
// });

Route::post('/izin/approve/{id}', [IzinController::class, 'approve']);
Route::post('/izin/reject/{id}', [IzinController::class, 'reject']);

Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
Route::post('/cuti', action: [CutiController::class, 'store'])->name(name: 'cuti.store');

Route::post('/absensi/store', [AbsensiController::class, 'store'])
    ->name('absensi.store')
    ->middleware('auth:karyawans');

Route::post('/absensi/scan', [AbsensiController::class, 'scan'])
    ->name('absensi.scan')
    ->middleware('auth:karyawans');


// Route::post('/data_py/delete', [EmployeeController::class, 'destroy'])->name('data_py.delete');
// Route::post('/data_py/edit/{id}', [EmployeeController::class, 'update'])->name(name: 'data_py.edit');
// Route::post('/data-py/save-all', [EmployeeController::class, 'saveAll'])->name('data_py.saveAll');
// Route::get('/data_py/edit/{id}', [EmployeeController::class, 'edit'])->name('data_py.edit');
// Route::put('/data_py/update/{id}', [EmployeeController::class, 'update'])->name('data_py.update');

// Route::get('/shifts/all', [ShiftController::class, 'getAll']);
// Route::post('/shifts/tambah', [ShiftController::class, 'store']);
// Route::post('/shifts/update-multiple', [ShiftController::class, 'updateMultiple']);
// Route::post('/shifts/delete-multiple', [ShiftController::class, 'deleteMultiple']);

// Route::post('/shifts/store-multiple', [ShiftController::class, 'storeMultiple']);

// Route::middleware('auth')->post('/izin/store', [IzinController::class, 'store']);

// Route::post('/izin/store', [IzinController::class, 'store'])->name('izin.store');


// Route::middleware(['auth:karyawan'])->group(function () {
//     Route::post('/cuti/store', [CutiController::class, 'store']);
//     Route::post('/izin/store', [IzinController::class, 'store']);
// });
