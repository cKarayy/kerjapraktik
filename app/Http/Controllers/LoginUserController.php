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
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Admin
        $admin = Admin::where('username', $request->username)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('admin.dashboard')]);
        }

        // Penyelia
        $penyelia = Penyelia::where('username', $request->username)->first();
        if ($penyelia && Hash::check($request->password, $penyelia->password)) {
            Auth::guard('penyelia')->login($penyelia);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('dashboard_py')]);
        }

        // Pegawai
        $pegawai = Employee::where('username', $request->username)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            Auth::guard('karyawans')->login($pegawai);
            return response()->json(['status' => 'success', 'message' => 'Login berhasil', 'redirectUrl' => route('pegawai.home')]);
        }


        return response()->json(['status' => 'error', 'message' => 'Username atau password salah.']);
    }


    public function home()
    {
        // Cek jenis pengguna yang login dan arahkan ke dashboard yang sesuai
        if (auth('admin')->check()) {
            $admin = auth('admin')->user();
            return redirect()->route('admin.dashboard');
        } elseif (auth('penyelia')->check()) {
            $penyelia = auth('penyelia')->user();
            return redirect()->route('dashboard_py');
        } elseif (auth('karyawans')->check()) {
            $pegawai = auth('karyawans')->user();
            return view('pegawai.home', compact('pegawai'));
        }

        return redirect()->route('pegawai.loginPg');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('penyelia')->logout();
        Auth::guard('karyawans')->logout();
        session()->flush();

        $response = redirect()->route('pegawai.loginPg');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        return $response;
    }

    public function verifyUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = Admin::where('username', $request->username)->first()
            ?: Penyelia::where('username', $request->username)->first()
            ?: Employee::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna ditemukan, silakan masukkan password baru.',
            'user_id' => $user->id_karyawan,  // Pastikan id_karyawan ada di model
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/[A-Z]/',  // Harus mengandung huruf kapital
                'regex:/[0-9]/',  // Harus mengandung angka
            ],
            'new_password_confirmation' => 'required|string|same:new_password',
            'user_id' => 'required|string',
        ]);

        try {
            $user = Admin::where('id_admin', $request->user_id)
                ?: Penyelia::where('id_penyelia', $request->user_id)
                ?: Employee::where('id_karyawan', $request->user_id)
                ->first();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'Password berhasil direset.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui password.']);
        }
    }
}
