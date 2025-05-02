<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\shifts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function create()
    {
        $shifts = shifts::all();
        return view('pegawai.registerPg', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'shift' => 'required|string|exists:shifts,nama_shift',
            'status' => 'required|string|in:active,inactive',
            'password' => 'required|string|confirmed|min:6',
        ]);

        Employee::create([
            'nama_lengkap' => Str::title($request->full_name),
            'jabatan' => strtoupper($request->jabatan),
            'id_shift' => shifts::where('nama_shift', $request->shift)->first()->id_shift,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pegawai.loginPg')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function showRegister()
    {
        $shifts = shifts::all(); // Ambil semua shift dari DB
        return view('pegawai.registerPg', compact('shifts'));
    }


}
