<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\LaporanKehadiranController;
use App\Http\Controllers\EmployeeController;
use App\Models\Employee;

//admin
Route::get('/', function () {
    return view('admin.welcome');
});

// Route::get('/admin/register', [AdminController::class, 'showRegister'])->name('admin.register');
// Route::post('/admin/register', [AdminController::class, 'registerAdmin'])->name('admin.register.submit');

// Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login');
// Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

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

// Route::get('/qrcode', function () {
//     return view('admin.qr');
// })->name('qrcode');

Route::get('/generate-qr', [QRController::class, 'generate'])->name('qrcode');
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

Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/admin/employees/store', [EmployeeController::class, 'store'])->name('employees.store');

//Penyelia
Route::get('/dashboard_py', function () {
    return view('penyelia.db');
})->name('dashboard_py');

Route::get('/data_py', function () {
    $employees = Employee::all()->map(function ($employee, $index) {
        return [
            'id' => $index + 1,
            'name' => $employee->name,
            'role' => $employee->role,
            'status' => $employee->status,
            'shift' => $employee->shift,
            'photo' => $employee->photo, // jika perlu tampilkan foto
        ];
    });

    return view('penyelia.dataPenyelia', compact('employees'));
})->name('data_py');

// Route::post('/data_py/add', function (Illuminate\Http\Request $request) {
//     foreach ($request->employees as $data) {
//         Employee::create([
//             'name' => $data['name'],
//             'role' => $data['role'],
//             'status' => $data['status'],
//             'shift' => $data['shift'],
//         ]);
//     }

//     return redirect()->route('data_py')->with('success', 'Pegawai berhasil ditambahkan!');
// })->name('data_py.add');

Route::post('/data_py/add', [EmployeeController::class, 'add'])->name('data_py.add');
Route::post('/data_py/delete', [EmployeeController::class, 'delete'])->name('data_py.delete');
Route::post('/data_py/edit', [EmployeeController::class, 'update'])->name(name: 'data_py.edit');
Route::post('/data-py/save-all', [EmployeeController::class, 'saveAll'])->name('data_py.saveAll');

// Route::get('/pegawai', [EmployeeController::class, 'index']);  // Menampilkan semua pegawai
// Route::post('/pegawai', [EmployeeController::class, 'store']); // Menyimpan pegawai baru
// Route::get('/pegawai/{id}', [EmployeeController::class, 'show']);  // Menampilkan detail pegawai
// Route::put('/pegawai/{id}', [EmployeeController::class, 'update']);  // Mengupdate pegawai
// Route::delete('/pegawai/{id}', [EmployeeController::class, 'destroy']);

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

