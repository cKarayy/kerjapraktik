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
        // Validasi nama lengkap
        $request->validate([
            'full_name' => 'required|string',
        ]);

        // Cari pengguna berdasarkan nama lengkap di ketiga model
        $user = Admin::where('nama_lengkap', $request->full_name)->first()
            ?: Penyelia::where('nama_lengkap', $request->full_name)->first()
            ?: Employee::where('nama_lengkap', $request->full_name)->first();

        // Cek apakah pengguna ditemukan
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
        }

        // Mengembalikan ID karyawan atau ID pengguna lainnya (yang digunakan untuk memperbarui password)
        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna ditemukan, silakan masukkan password baru.',
            'user_id' => $user->id_karyawan,  // Mengembalikan id_karyawan yang digunakan di updatePassword
        ]);
    }

    public function updatePassword(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed',  // Laravel memeriksa new_password_confirmation
                'regex:/[A-Z]/',  // Harus mengandung huruf kapital
                'regex:/[0-9]/',  // Harus mengandung angka
            ],
            'new_password_confirmation' => 'required|string|same:new_password', // Pastikan konfirmasi password sama dengan password baru
            'user_id' => 'required|string',  // Pastikan user_id dikirim
        ]);

        try {
            // Cari pengguna berdasarkan nama lengkap atau ID karyawan (user_id)
            $user = Admin::where('id_admin', $request->user_id)
                ?: Penyelia::where('id_penyelia', $request->user_id)
                ?: Employee::where('id_karyawan', $request->user_id)
                ->first();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
            }

            // Update password pengguna
            $user->password = Hash::make($request->new_password);  // Encrypt password
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'Password berhasil direset.']);
        } catch (\Exception $e) {
            // Tangani error dan kembalikan respons JSON
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui password.']);
        }
    }
}
