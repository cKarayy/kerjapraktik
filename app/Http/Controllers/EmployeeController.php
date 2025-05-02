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

    public function update(Request $request, $id)
    {
        // Menggunakan nama variabel $pegawai
        $pegawai = Employee::findOrFail($id);
        $shift = Shifts::where('nama_shift', $request->input('shift'))->first();

        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'Shift tidak ditemukan.'], 404);
        }

        $pegawai->update([
            'nama_lengkap' => Str::title($request->input('name')),
            'jabatan' => strtoupper($request->input('role')),
            'id_shift' => $shift->id_shift,
            'status' => strtolower($request->input('status'))
        ]);

        return response()->json(['success' => true, 'message' => 'Data pegawai berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        // Mencari pegawai berdasarkan ID
        $pegawai = Employee::findOrFail($id);
        $pegawai->delete(); // Menghapus pegawai

        return response()->json(['success' => true, 'message' => 'Data pegawai berhasil dihapus.']);
    }

}
