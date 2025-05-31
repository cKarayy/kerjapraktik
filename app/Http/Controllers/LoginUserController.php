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

        // Coba login sebagai admin
        $admin = Admin::where('username', $request->username)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'redirectUrl' => route('admin.dashboard'),
                'userType' => 'admin' // Tambahkan identifikasi tipe user
            ]);
        }

        // Coba login sebagai penyelia
        $penyelia = Penyelia::where('username', $request->username)->first();
        if ($penyelia && Hash::check($request->password, $penyelia->password)) {
            Auth::guard('penyelia')->login($penyelia);
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'redirectUrl' => route('dashboard_py'),
                'userType' => 'penyelia' // Tambahkan identifikasi tipe user
            ]);
        }

        // Coba login sebagai pegawai
        $pegawai = Employee::where('username', $request->username)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            Auth::guard('karyawans')->login($pegawai);
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'redirectUrl' => route('pegawai.home'),
                'userType' => 'pegawai' // Tambahkan identifikasi tipe user
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Username atau password salah.'
        ]);
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
        try {
            $request->validate([
                'username' => 'required|string',
            ]);

            $user = Employee::where('username', $request->username)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pengguna ditemukan, silakan masukkan password baru.',
                'user_id' => $user->id_karyawan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

   public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'new_password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/'
                ],
                'new_password_confirmation' => 'required|string|same:new_password',
                'user_id' => 'required|integer|exists:karyawans,id_karyawan',
            ]);

            $user = Employee::where('id_karyawan', $request->user_id)->firstOrFail();

            // Update password di tabel Employee
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Sync password ke tabel Admin jika ada
            if ($user->role === 'admin') {
                $admin = Admin::where('username', $user->username)->first();
                if ($admin) {
                    $admin->password = Hash::make($request->new_password);
                    $admin->save();
                }
            }

            // Sync password ke tabel Penyelia jika ada
            if ($user->role === 'penyelia') {
                $penyelia = Penyelia::where('username', $user->username)->first();
                if ($penyelia) {
                    $penyelia->password = Hash::make($request->new_password);
                    $penyelia->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil direset.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }


}
