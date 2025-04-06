<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Fungsi untuk mendaftarkan admin
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255|unique:admins',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,penyelia',
        ]);

        // Cek jumlah penyelia
        if ($request->role === 'penyelia' && Admin::where('role', 'penyelia')->count() >= 2) {
            return redirect()->route('admin.register')->with('error', 'Penyelia sudah mencapai batas maksimal (2 orang).');
        }

        Admin::create([
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
            'role' => $request->role, // PASTIKAN ADA INI
        ]);

        return redirect()->route('admin.login')->with('success', 'Pendaftaran berhasil!');
    }


    // Menampilkan halaman register
    public function showRegister()
    {
        return view('admin.register');
    }

    public function penyeliaDashboard()
    {
        return view('penyelia.db');
    }

    public function admDashboard()
    {
        return view('admin.dashboard');
    }

}
