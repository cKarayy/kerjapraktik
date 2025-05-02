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
        if ($admin && Hash::check($request->password, $admin->password_admin)) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard');
        }

        // Penyelia
        $penyelia = Penyelia::where('nama_lengkap', $request->full_name)->first();
        if ($penyelia && Hash::check($request->password, $penyelia->password_penyelia)) {
            Auth::guard('penyelia')->login($penyelia);
            return redirect()->route('dashboard_py');
        }

        // Pegawai
        $pegawai = Employee::where('nama_lengkap', $request->full_name)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            Auth::guard('karyawans')->login($pegawai);

            // Mengirim data pegawai ke view menggunakan with()
            return redirect()->route('pegawai.home')->with('pegawai', $pegawai);
        }

        return redirect()->back()->with('error', 'Nama lengkap atau password salah.');
    }

    public function home()
    {
        // Ambil data pegawai yang login
        $pegawai = auth()->guard('karyawans')->user();  // Mengambil data user yang login

        // Kirimkan data pegawai ke view
        return view('pegawai.home', compact('pegawai'));
    }
    public function logout(Request $request)
    {
        Auth::guard('karyawans')->logout();
        session()->flush();

        return redirect()->route('pegawai.loginPg');
    }

}
