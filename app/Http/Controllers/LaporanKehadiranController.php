<?php

namespace App\Http\Controllers;

use App\Models\LaporanKehadiran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKehadiranController extends Controller
{
    public function getReport(Request $request)
    {
        // Ambil bulan dan tahun dari input atau set default ke bulan dan tahun sekarang
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m')); // Default ke bulan ini
        $shift = $request->input('shift'); // Ambil shift jika ada
        $keterangan = $request->input('keterangan'); // Ambil keterangan jika ada

        // Query untuk mengambil laporan berdasarkan bulan dan tahun
        $laporan = LaporanKehadiran::whereMonth('tanggal_scan', date('m', strtotime($bulan))) // Ambil laporan untuk bulan yang dipilih
            ->whereYear('tanggal_scan', date('Y', strtotime($bulan))) // Ambil laporan untuk tahun yang dipilih
            ->when($shift, function($query) use ($shift) {
                return $query->where('shift', $shift); // Filter berdasarkan shift jika ada
            })
            ->when($keterangan, function($query) use ($keterangan) {
                return $query->where('kehadiran', $keterangan); // Filter berdasarkan keterangan jika ada
            })
            ->get();

        // Kembalikan data ke view dengan nama 'admin.laporan' dan data laporan
        return view('admin.laporan', compact('laporan'));
    }
}
