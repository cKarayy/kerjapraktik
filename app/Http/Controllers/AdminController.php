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
        ]);

        // Membuat admin baru
        $admin = Admin::create([
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('admin.login')->with('success', 'Admin berhasil didaftarkan!');
    }

    // Menampilkan halaman register
    public function showRegister() {
        return view('admin.register');
    }

    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function loginAdmin(Request $request)
    {
        // Validasi inputan login
        $request->validate([
            'full_name' => 'required|string',
            'password' => 'required|min:6',
        ]);

        // Mencari admin berdasarkan nama lengkap
        $admin = Admin::where('full_name', $request->full_name)->first();

        // Memeriksa apakah admin ditemukan dan password cocok
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Mengautentikasi admin
            Auth::login($admin);
            Log::info('Login berhasil untuk admin: ' . $admin->full_name);

            return redirect()->route('admin.dashboard');

        } else {
            return redirect()->route('admin.login')->with('error', 'Nama lengkap atau password salah');
        }
    }

    public function dashboard()
    {
        // Mengecek apakah admin sudah login
        if (Auth::guard('admin')->check()) {
            return view('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->with('error', 'Anda belum login sebagai admin');
        }
    }

}
