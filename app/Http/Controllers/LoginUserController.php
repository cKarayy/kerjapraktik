<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class LoginUserController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('pegawai.loginPg');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'full_name' => 'required|string',
            'password' => 'required',
        ]);

        // Mencari pegawai berdasarkan nama lengkap
        $pegawai = Employee::where('nama_lengkap', $request->full_name)->first();

        // Jika pegawai ditemukan dan password cocok
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            // Melakukan login dengan guard karyawans
            Auth::guard('web')->login($pegawai);

            // Cek role dan arahkan ke dashboard sesuai role
            if ($pegawai->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($pegawai->role == 'penyelia') {
                return redirect()->route('dashboard_py');
            } else {
                return redirect()->route('pegawai.home');
            }
        }

        // Jika login gagal
        return redirect()->back()->with('error', 'Nama lengkap atau password salah.');
    }

    // Halaman utama setelah login untuk Pegawai
    public function home()
    {
        // Cek jika pegawai sudah login
        if (!Auth::guard('web')->check()) {
            return redirect()->route('pegawai.loginPg')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil data pegawai yang login
        $pegawai = Auth::guard('web')->user(); // Memastikan menggunakan guard 'web' untuk mendapatkan user

        // Kirimkan data pegawai ke view
        return view('pegawai.home', compact('pegawai'));

        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
    }

       public function showChangePasswordForm()
    {
        return view('pegawai.changePassword');
    }

    public function updatePassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Mendapatkan pengguna yang sedang login
        $user = Auth::guard('karyawans')->user();  // Pastikan Anda menggunakan guard yang tepat

        // Cek apakah password lama yang dimasukkan cocok dengan password di database
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama tidak cocok.']);
        }

        // Update password dengan password baru
        $user->password = Hash::make($request->new_password);

        // Simpan perubahan ke database
        if ($user->save()) {
            return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui password.']);
        }
    }


    // Logout
    public function logout(Request $request)
    {
        // Logout pegawai
        Auth::guard('web')->logout();
        session()->flush();  // Menghapus session setelah logout

        // Redirect ke halaman login dengan status logout
        return redirect()->route('pegawai.loginPg')->with('loggedOut', true);
    }

      public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function penyeliaDashboard()
    {
        return view('dashboard_py');
    }

    public function pegawaiDashboard()
    {
        return view('pegawai.home');
    }


}
