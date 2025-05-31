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

         $messages = [
            'password.regex' => 'Password harus mengandung minimal satu huruf besar dan satu angka.',
            'password.min' => 'Password minimal 6 karakter.',
        ];

        // Validasi input dari form
        $request->validate([
            'username' => 'required|string|max:255|unique:karyawans,username',  // Validasi untuk username
            'nama_lengkap' => 'required|string|max:255',  // Validasi untuk nama lengkap
            'jabatan' => 'required|string|max:100',
            'shift' => 'required|string|exists:shifts,nama_shift',  // Pastikan shift ada di database
            'role' => 'required|string|in:pegawai,admin,penyelia',  // Pastikan role sesuai
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ], // Validasi password
        ], $messages);


        // Cari shift berdasarkan nama_shift
        $shift = shifts::where('nama_shift', $request->shift)->first();

        if (!$shift) {
            return back()->withErrors(['shift' => 'Shift tidak ditemukan.'])->withInput();
        }

        $role = $request->role ?? 'pegawai';

        // Membuat data pegawai di tabel Employee
        $employee = Employee::create([
            'nama_lengkap' => Str::title($request->nama_lengkap),
            'username' => $request->username,  // Pastikan username sudah ada
            'jabatan' => strtoupper($request->jabatan),
            'id_shift' => $shift->id_shift,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        // Simpan berdasarkan role
        if ($request->role == 'admin') {
            Admin::create([
                'nama_lengkap' => $employee->nama_lengkap,
                'username' => $employee->username,  // Pastikan username disalin dari employee
            ]);
        } elseif ($request->role == 'penyelia') {
            Penyelia::create([
                'nama_lengkap' => $employee->nama_lengkap,
                'username' => $employee->username,  // Pastikan username disalin dari employee
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
