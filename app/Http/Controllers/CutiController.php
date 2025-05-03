<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;

class CutiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string'
        ]);

        Cuti::create([
            'id_karyawan' => auth('employee')->user()->id_karyawan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil dikirim.');
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
