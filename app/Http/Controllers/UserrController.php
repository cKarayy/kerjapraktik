<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserrController extends Controller
{
    // Fungsi untuk mendaftarkan pegawai
    // Tampilkan form register
    public function showRegister()
    {
        return view('pegawai.register');
    }

    // Proses register
    public function registerPegawai(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'jabatan' => 'required|in:pegawai',
            'shift' => 'required|in:pagi,middle,malam',
            'status' => 'required|in:active,inactive'
        ]);

        $namaLengkap = $request->full_name;

        if ($request->role === 'pegawai') {
            Employee::create([
                'nama_lengkap' => $namaLengkap,
                'password_pegawai' => Hash::make($request->password),
            ]);
        } else {

        }

        return redirect()->route('pegawai.login')->with('success', 'Registrasi berhasil! Silakan login.');
    }


    // Tampilkan form login
    public function showLoginForm()
    {
        return view('pegawai.login');
    }


    public function pgDashboard()
    {
        return view(view: 'pegawai.home');
    }
}
