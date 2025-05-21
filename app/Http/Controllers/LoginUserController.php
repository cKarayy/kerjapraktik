<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\Employee;

class LoginUserController extends Controller
{
    public function showLoginForm()
    {
        return view('pegawai.loginPg');
    }

    public function login(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'password' => 'required',
        ]);

        // Admin
        $admin = Admin::where('nama_lengkap', $request->full_name)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('admin.dashboard')]);
        }

        // Penyelia
        $penyelia = Penyelia::where('nama_lengkap', $request->full_name)->first();
        if ($penyelia && Hash::check($request->password, $penyelia->password)) {
            Auth::guard('penyelia')->login($penyelia);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('dashboard_py')]);
        }

        // Pegawai
        $pegawai = Employee::where('nama_lengkap', $request->full_name)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            Auth::guard('karyawans')->login($pegawai);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('pegawai.home')]);
        }

        return response()->json(['status' => 'error', 'message' => 'Nama lengkap atau password salah.']);
    }

    public function home()
    {
        // Cek jenis pengguna yang login dan arahkan ke dashboard yang sesuai
        if (auth('admin')->check()) {
            // Admin
            $admin = auth('admin')->user();
            return redirect()->route('admin.dashboard');
        } elseif (auth('penyelia')->check()) {
            // Penyelia
            $penyelia = auth('penyelia')->user();
            return redirect()->route('dashboard_py');
        } elseif (auth('karyawans')->check()) {
            // Pegawai
            $pegawai = auth('karyawans')->user();
            return view('pegawai.home', compact('pegawai'));
        }

        // Jika tidak ada yang login, redirect ke halaman login
        return redirect()->route('pegawai.loginPg');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('penyelia')->logout();
        Auth::guard('karyawans')->logout();
        session()->flush(); // Menghapus semua data sesi

        return redirect()->route('pegawai.loginPg');
    }


   public function verifyUser(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
        ]);

        // Mencari pengguna di ketiga model dalam satu query
        $user = Admin::where('nama_lengkap', $request->full_name)
            ->first() ?: Penyelia::where('nama_lengkap', $request->full_name)
            ->first() ?: Employee::where('nama_lengkap', $request->full_name)
            ->first();

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
        }

        return response()->json(['status' => 'success', 'message' => 'Pengguna ditemukan, silakan masukkan password baru.']);
    }

   public function updatePassword(Request $request)
    {
        // Validasi input password
        $request->validate([
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Mendefinisikan variabel user
        $user = null;

        // Periksa apakah pengguna login dengan guard yang sesuai
        if (auth('admin')->check()) {
            $user = auth('admin')->user();  // Mendapatkan pengguna admin
        } elseif (auth('penyelia')->check()) {
            $user = auth('penyelia')->user();  // Mendapatkan pengguna penyelia
        } elseif (auth('karyawans')->check()) {
            $user = auth('karyawans')->user();  // Mendapatkan pengguna pegawai
        }

        // Jika tidak ada pengguna yang login, kembalikan error
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak terautentikasi']);
        }

        // Debugging: periksa apakah $user adalah model yang benar
        dd($request->new_password, $user->password);
        // Update password pengguna
        $user->password = Hash::make($request->new_password);
        dd($user->getOriginal('password'), $user->password);
        $user->save();  // Simpan perubahan password

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui']);
    }



}
