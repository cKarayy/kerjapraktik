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
            'shift' => 'required|string|exists:shifts,nama_shift',
            'password' => 'required|string|confirmed|min:6',
            'username' => 'required|string|unique:karyawans,username',  // Menambahkan validasi untuk username yang unik
        ]);

        $shift = Shifts::where('nama_shift', $request->shift)->first();

        if (!$shift) {
            return back()->withErrors(['shift' => 'Shift tidak ditemukan.'])->withInput();
        }

        // Ambil username yang dimasukkan oleh pengguna
        $username = strtolower(str_replace(' ', '.', $request->username));  // Gunakan username dari input form

        // Menentukan role berdasarkan kode angka di belakang username
        $role = 'pegawai'; // Default role
        if (str_ends_with($username, '1')) {
            $role = 'admin';
        } elseif (str_ends_with($username, '2')) {
            $role = 'pegawai';
        }

        // Jika status tidak diberikan, set default ke 'active'
        $status = $request->status ?? 'active'; // Menetapkan status default ke active jika tidak diberikan

        Employee::create([
            'nama_lengkap' => Str::title($request->full_name),
            'jabatan' => strtoupper($request->jabatan),
            'id_shift' => $shift->id_shift, // Gunakan id_shift dari data shift yang ditemukan
            'status' => $status, // Menggunakan status dari form atau default
            'password' => Hash::make($request->password),
            'username' => $username, // Menggunakan username dari input form
            'role' => $role, // Menetapkan role berdasarkan kode angka di belakang username
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

}
