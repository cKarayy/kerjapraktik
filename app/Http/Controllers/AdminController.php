<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Fungsi untuk mendaftarkan admin atau penyelia
    // Tampilkan form register
    public function showRegister()
    {
        return view('admin.register');
    }

    // Proses register
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,penyelia',
        ]);

        $namaLengkap = $request->full_name;

        if ($request->role === 'admin') {
            Admin::create([
                'nama_lengkap' => $namaLengkap,
                'password_admin' => Hash::make($request->password),
            ]);
        } else {
            if (Penyelia::count() >= 2) {
                return redirect()->back()->with('error', 'Maksimal penyelia hanya 2 orang.');
            }

            Penyelia::create([
                'nama_lengkap' => $namaLengkap,
                'password_penyelia' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.login')->with('success', 'Registrasi berhasil! Silakan login.');
    }


    // Tampilkan form login
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Dashboard penyelia
    public function penyeliaDashboard()
    {
        return view('penyelia.db');
    }

    // Dashboard admin
    public function admDashboard()
    {
        return view('admin.dashboard');
    }

    public function pgDashboard()
    {
        return view(view: 'pegawai.home');
    }
}
