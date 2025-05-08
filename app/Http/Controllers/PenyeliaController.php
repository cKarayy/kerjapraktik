<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Cuti;
use Carbon\Carbon;

class PenyeliaController extends Controller
{
    public function showLaporan(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $shift = $request->input('shift');
        $keterangan = $request->input('keterangan');

        $startDate = Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth();

        // Ambil data izin dan cuti yang diajukan oleh pegawai
        $laporan = collect();

        if ($keterangan === 'izin') {
            $laporan = Izin::whereBetween('tanggal', [$startDate, $endDate])->get();
        } elseif ($keterangan === 'cuti') {
            $laporan = Cuti::whereBetween('tanggal_mulai', [$startDate, $endDate])->get();
        }

        return view('penyelia.laporanPy', compact('laporan'));
    }
}
