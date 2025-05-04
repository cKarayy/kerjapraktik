<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\shifts;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Menerima data scan QR Code dan mencatat absensi.
     */
    public function scan(Request $request)
    {
        // Validasi input dari QR code
        $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id_karyawan',
            'id_shift' => 'required|exists:shifts,id_shift',
            'waktu_scan' => 'required|date',
        ]);

        // Ambil data shift untuk menghitung keterlambatan
        $shift = shifts::find($request->id_shift);
        $waktu_shift = Carbon::parse($shift->waktu_mulai);
        $waktu_scan = Carbon::parse($request->waktu_scan);

        // Hitung keterlambatan (dalam menit)
        $keterlambatan = $waktu_scan->diffInMinutes($waktu_shift, false);
        $keterlambatan = $keterlambatan > 0 ? $keterlambatan : 0;  // Tidak negatif

        // Tentukan status kehadiran (misal hadir, terlambat, atau lainnya)
        $kehadiran = $keterlambatan > 0 ? 'Terlambat' : 'Hadir';

        // Buat data absensi baru berdasarkan scan QR code
        Absensi::create([
            'id_karyawan' => $request->id_karyawan,
//            'id_shift' => $request->id_shift,
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_masuk' => $waktu_scan,
            'kehadiran' => $kehadiran,
            'keterlambatan' => $keterlambatan,
        ]);

        // Redirect atau response dengan pesan sukses
        return response()->json(['message' => 'Absensi berhasil dicatat', 'status' => 'success']);
    }
}
