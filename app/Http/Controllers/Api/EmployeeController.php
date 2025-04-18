<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Menampilkan daftar pegawai
    public function index()
    {
        $employees = Employee::all(); // Ambil semua data pegawai
        return response()->json($employees); // Kembalikan data dalam format JSON
    }

    // Menyimpan data pegawai baru
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'status' => 'required|string',
            'shift' => 'required|string',
        ]);

        // Simpan data pegawai
        $employee = Employee::create($validated);

        // Kembalikan data pegawai yang baru dibuat
        return response()->json($employee, 201);
    }

    // Menampilkan detail pegawai
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    // Mengupdate data pegawai
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Validasi dan update data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'status' => 'required|string',
            'shift' => 'required|string',
        ]);

        $employee->update($validated);

        return response()->json($employee);
    }

    // Menghapus data pegawai
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
