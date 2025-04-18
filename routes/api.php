<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

// API routes for employees
Route::middleware('auth:api')->group(function () {
    // Menampilkan data pegawai
    Route::get('/pegawai', [EmployeeController::class, 'index']); // GET /api/pegawai

    // Menambahkan data pegawai
    Route::post('/pegawai', [EmployeeController::class, 'store']); // POST /api/pegawai

    // Mengupdate data pegawai
    Route::put('/pegawai/{id}', [EmployeeController::class, 'update']); // PUT /api/pegawai/{id}

    // Menghapus data pegawai
    Route::delete('/pegawai/{id}', [EmployeeController::class, 'destroy']); // DELETE /api/pegawai/{id}
});
