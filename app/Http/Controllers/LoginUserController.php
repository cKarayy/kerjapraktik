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

        // Cari user di ketiga model berdasarkan nama lengkap
        $user = Admin::where('nama_lengkap', $request->full_name)->first()
            ?: Penyelia::where('nama_lengkap', $request->full_name)->first()
            ?: Employee::where('nama_lengkap', $request->full_name)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
        }

        // Kita bisa return juga tipe user untuk update nanti
        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna ditemukan, silakan masukkan password baru.',
            'user_type' => get_class($user),   // misal: App\Models\Admin, App\Models\Penyelia, atau App\Models\Employee
            'user_id' => $user->id,             // untuk identifikasi user di update password
        ]);
    }

   public function updatePassword(Request $request)
    {
        $request->validate([
            'user_type' => 'required|string',
            'user_id' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $modelClass = $request->user_type;

        if (!in_array($modelClass, [Admin::class, Penyelia::class, Employee::class])) {
            return response()->json(['status' => 'error', 'message' => 'Tipe pengguna tidak valid.']);
        }

        $user = $modelClass::find($request->user_id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password); // atau setter kalau kamu pakai
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil direset.']);
    }

}
