<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Employee;

class CutiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string'
        ]);

        $idKaryawan = auth(guard: 'karyawans')->user()->id_karyawan;
        $karyawan = Employee::find($idKaryawan);

        $cuti = Cuti::create([
            'id_karyawan' => $idKaryawan,
            'id_shift' => $karyawan->id_shift,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
        ]);

        // Mengembalikan response dengan data yang sesuai
        return response()->json([
            'success' => true,
            'message' => 'Cuti berhasil diajukan',
            'data' => $cuti // Menambahkan data cuti yang baru dibuat
        ]);
    }


    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'disetujui',
            'id_penyelia' => auth('penyelia')->user()->id_penyelia
        ]);

        return redirect()->back();
    }

    public function reject($id)
    {
        $cuti = Cuti::find($id);

        if (!$cuti) {
            return redirect()->back()->with('error', 'Data cuti tidak ditemukan.');
        }

        if (!auth('penyelia')->check()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $cuti->status = 'ditolak';
        $cuti->id_penyelia = auth('penyelia')->user()->id_penyelia;
        $cuti->save();

        return redirect()->back()->with('success', 'Cuti berhasil ditolak.');
    }

}
