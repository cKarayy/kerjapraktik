<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\QR;
use App\Models\shifts;
use Carbon\Carbon;

class AbsensiController extends Controller
{

public function scan(Request $request)
    {
        // Mendapatkan data yang dikirimkan oleh frontend
        $idKaryawan = $request->input('id_karyawan');  // ID karyawan yang sedang login
        $idShift = $request->input('id_shift');  // ID shift dari frontend
        $status = $request->input('status');  // Status kehadiran (Hadir)
        $waktuMasuk = $request->input('waktu_masuk');  // Waktu absensi
        $idCode = $request->input('id_code');  // ID QR Code dari hasil scan
        $keterlambatan = $request->input('keterlambatan');  // Keterlambatan dalam menit

        // Validasi apakah id_code valid (ada di tabel QR Code)
        $qrCode = QR::find($idCode);  // Pastikan menggunakan model QR yang benar
        if (!$qrCode) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid.']);
        }

        // Menyimpan data absensi ke tabel Absensi
        $absensi = new Absensi();
        $absensi->id_karyawan = $idKaryawan;
        $absensi->id_admin = 1;  // Admin ID bisa disesuaikan (misalnya admin yang login)
        $absensi->tanggal = Carbon::today();  // Tanggal absensi menggunakan Carbon (hari ini)
        $absensi->waktu_masuk = Carbon::parse($waktuMasuk);  // Waktu saat absensi
        $absensi->kehadiran = $status;  // Status kehadiran (Hadir)
        $absensi->id_shift = $idShift;  // ID shift pegawai
        $absensi->keterlambatan = $keterlambatan;  // Keterlambatan dalam menit
        $absensi->id_code = $idCode;  // ID QR code
        $absensi->save();

        return response()->json(['success' => true, 'message' => 'Absensi berhasil dicatat!']);
    }

    public function store(Request $request)
    {
        // Validasi input (opsional, tapi disarankan)
        $request->validate([
            'id_karyawan' => 'required|exists:employees,id', // Pastikan ID karyawan valid
        ]);

        // Ambil id_code terbaru dari tabel qr_code
        $idCodeTerbaru = QR::orderBy('waktu_generate', 'desc')
            ->value('id_code');

        if (!$idCodeTerbaru) {
            return response()->json([
                'success' => false,
                'message' => 'Kode QR belum tersedia.',
            ], 404);
        }

        // Simpan data absensi ke dalam tabel absensi
        Absensi::create([
            'id_karyawan' => $request->id_karyawan,
            'id_code' => $idCodeTerbaru,
            'waktu_absen' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan.',
        ]);
    }


}
