<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $idKaryawan = auth(guard: 'karyawans')->user()->id_karyawan;
        $karyawan = Employee::find($idKaryawan);

        Izin::create([
            'id_karyawan' => $idKaryawan,
            'id_shift' => $karyawan->id_shift, // ambil dari tabel karyawans
            'alasan' => $request->alasan,
            'tanggal' => now(),
            'status' => 'menunggu',
        ]);

    }

    public function approve(Request $request, $id)
    {
        $id_penyelia = $request->input('id_penyelia');  // ID penyelia dikirimkan melalui request

        if (!$id_penyelia) {
            return response()->json(['error' => 'Akses ditolak. Penyelia tidak teridentifikasi.'], 403);
        }

        $izin = Izin::findOrFail($id);
        $izin->status = 'disetujui';
        $izin->id_penyelia = $id_penyelia;
        $izin->save();

        return response()->json(['message' => 'Izin disetujui'], 200);
    }



    public function reject($id)
    {
        // Mencari izin berdasarkan ID
        $izin = Izin::findOrFail($id);

        // Cek jika status izin sudah diproses (disetujui/ditolak)
        if (in_array($izin->status, ['disetujui', 'ditolak'])) {
            return response()->json(['error' => 'Izin sudah diproses'], 400);
        }

        // Pastikan pengguna adalah penyelia
        if (!auth('penyelia')->check()) {
            return response()->json(['error' => 'Akses ditolak. Anda bukan penyelia.'], 403);
        }

        // Mengambil data penyelia yang sedang login
        $penyelia = auth('penyelia')->user();

        // Mengupdate status izin menjadi 'ditolak' dan menyimpan ID penyelia
        $izin->status = 'ditolak';
        $izin->id_penyelia = $penyelia->id_penyelia;
        $izin->save();

        // Kembalikan response sukses dalam format JSON
        return response()->json(['message' => 'Izin berhasil ditolak.'], 200);
    }
}
