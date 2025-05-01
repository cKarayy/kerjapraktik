<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\Employee;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login'); // Ubah jika view login-nya gabungan
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
            return redirect()->route('pegawai.home');
        }

        return redirect()->back()->with('error', 'Nama lengkap atau password salah.');
    }


    // Logout
    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('admin.login');
    }
}
