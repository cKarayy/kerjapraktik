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
        Log::info('registerAdmin() dipanggil', ['request_data' => $request->all()]);

        $request->validate([
            'full_name' => 'required|string|max:255|unique:admins',
            'password' => 'required|min:6|confirmed',
        ]);

        // Membuat admin baru
        $admin = Admin::create([
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
        ]);

        Log::info('Admin berhasil didaftarkan', ['admin' => $admin]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('admin.login')->with('success', 'Admin berhasil didaftarkan!');
    }

    // Menampilkan halaman register
    public function showRegister()
    {
        Log::info('Menampilkan halaman register');
        return view('admin.register');
    }

}
