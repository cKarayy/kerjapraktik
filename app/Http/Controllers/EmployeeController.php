<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shifts; // Pastikan menggunakan 'Shifts' dengan huruf kapital
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function create()
    {
        $shifts = Shifts::all(); // Pastikan shift diambil dari model Shifts yang benar
        return view('pegawai.registerPg', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'shift' => 'required|string|exists:shifts,nama_shift', // Pastikan shift ada di database
            'status' => 'required|string|in:active,inactive',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $shift = Shifts::where('nama_shift', $request->shift)->first();

        if (!$shift) {
            return back()->withErrors(['shift' => 'Shift tidak ditemukan.'])->withInput();
        }

        Employee::create([
            'nama_lengkap' => Str::title($request->full_name),
            'jabatan' => strtoupper($request->jabatan),
            'id_shift' => $shift->id_shift, // Gunakan id_shift dari data shift yang ditemukan
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pegawai.loginPg')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function showRegister()
    {
        $shifts = Shifts::all(); // Ambil semua shift dari DB
        return view('pegawai.registerPg', compact('shifts'));
    }

     // Menampilkan data pegawai
     public function index()
     {
         // Ambil data pegawai dan shift dari database
         $employees = Employee::all();
         $shifts = shifts::all(); // Ambil data shift dari database

         // Kirim data pegawai dan shift ke view
         return view('employee.edit', compact('employees', 'shifts'));
     }

     public function update(Request $request, $id)
     {
         $employee = Employee::find($id);

         if ($employee) {
             $employee->status = $request->status;

             // Cari ID shift berdasarkan nama shift
             $shift = Shifts::where('nama_shift', $request->shift)->first();
             if ($shift) {
                 $employee->id_shift = $shift->id_shift;
             }

             $employee->save();

             return response()->json(['message' => 'Data berhasil diperbarui!']);
         }

         return response()->json(['message' => 'Pegawai tidak ditemukan.'], 404);
     }


     // Menghapus pegawai
     public function delete($id)
    {
        try {
            $pegawai = Employee::findOrFail($id);
            $pegawai->delete();
            return response()->json(['message' => 'Pegawai berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus pegawai'], 400);
        }
    }

}
