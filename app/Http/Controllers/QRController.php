<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Absensi;
use Carbon\Carbon;

class QRController extends Controller
{
    public function generateQR()
    {
        $randomCode = uniqid(); // Kode unik setiap 5 detik
        $qr = QrCode::size(200)->generate($randomCode);

        return response()->json([
            'qr' => base64_encode($qr),
            'code' => $randomCode
        ]);
    }

    // Simpan ke database saat QR Code dipindai
    public function scanQR(Request $request)
    {
        $kode = $request->input('code');

        // Simpan laporan absensi pegawai
        Absensi::create([
            'pegawai_id' => auth()->guard('admin')->id(), // Pegawai yang login
            'qr_code' => $kode,
            'scan_time' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Absensi berhasil tercatat!']);
    }
}
