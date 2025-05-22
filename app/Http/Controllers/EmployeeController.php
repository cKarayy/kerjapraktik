<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\shifts; // Pastikan menggunakan 'Shifts' dengan huruf kapital
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function create()
    {
        $shifts = shifts::all(); // Mengambil semua data shift dari database
        return view('pegawai.registerPg', compact('shifts')); // Mengirim data shift ke view
    }

    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'full_name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'shift' => 'required|string|exists:shifts,nama_shift', // Pastikan shift ada di database
            'role' => 'required|string|in:pegawai,admin,penyelia', // Pastikan role sesuai
            'password' => 'required|string|confirmed|min:6',
        ]);

        // Mencari data shift berdasarkan nama_shift
        $shift = shifts::where('nama_shift', $request->shift)->first();

        if (!$shift) {
            return back()->withErrors(['shift' => 'Shift tidak ditemukan.'])->withInput();
        }

        // Membuat data pegawai di tabel Employee
        $employee = Employee::create([
            'nama_lengkap' => Str::title($request->full_name),
            'jabatan' => strtoupper($request->jabatan),
            'id_shift' => $shift->id_shift, // Gunakan id_shift yang ditemukan
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Jika role adalah 'Admin', simpan ke tabel Admin
        if ($request->role == 'admin') {
            Admin::create([
                'nama_lengkap' => $employee->nama_lengkap,
                'password' => $employee->password,
            ]);
        }
        // Jika role adalah 'Penyelia', simpan ke tabel Penyelia
        elseif ($request->role == 'penyelia') {
            Penyelia::create([
                'nama_lengkap' => $employee->nama_lengkap,
                'password' => $employee->password,
            ]);
        }

        // Pengalihan ke halaman login setelah registrasi berhasil
        return redirect()->route('pegawai.loginPg')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Menampilkan halaman registrasi
    public function showRegister()
    {
        $shifts = shifts::all(); // Mengambil semua shift dari database
        return view('pegawai.registerPg', compact('shifts')); // Mengirim data shift ke view
    }

     public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|string',
            'shift'  => 'required|string',
            'role'   => 'required|string',
        ]);

        // Temukan data pegawai berdasarkan ID
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Pegawai tidak ditemukan.'], 404);
        }

        // Update status dan role (alias dari jabatan)
        $employee->status  = $request->status;
        $employee->jabatan = $request->role;

        // Cari ID shift berdasarkan nama shift
        $shift = shifts::where('nama_shift', $request->shift)->first();

        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan.'], 404);
        }

        $employee->id_shift = $shift->id_shift;

        // Simpan perubahan
        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!',
            'data'    => [
                'id'     => $employee->id,
                'status' => $employee->status,
                'shift'  => $shift->nama_shift,
                'role'   => $employee->jabatan,
            ]
        ]);
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

     public function index()
     {
         // Ambil data pegawai dan shift dari database
         $employees = Employee::all();
         $shifts = shifts::all(); // Ambil data shift dari database

         // Kirim data pegawai dan shift ke view
         return view('employee.edit', compact('employees', 'shifts'));
     }

}
