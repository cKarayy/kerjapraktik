<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;

class IzinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        Izin::create([
            'id_karyawan' => auth('employee')->user()->id_karyawan,
            'tanggal' => now()->toDateString(),
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Izin berhasil dikirim.');
    }

    public function approve($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->update([
            'status' => 'disetujui',
            'id_penyelia' => auth('penyelia')->user()->id_penyelia
        ]);

        return redirect()->back();
    }

    public function reject($id)
    {
        $izin = Izin::find($id);

        if (!$izin) {
            return redirect()->back()->with('error', 'Data izin tidak ditemukan.');
        }

        // Gunakan guard penyelia
        if (!auth('penyelia')->check()) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan penyelia.');
        }

        $penyelia = auth('penyelia')->user();

        $izin->status = 'ditolak';
        $izin->id_penyelia = $penyelia->id_penyelia;
        $izin->save();

        return redirect()->route('izin.index')->with('success', 'Izin berhasil ditolak.');
    }

}
