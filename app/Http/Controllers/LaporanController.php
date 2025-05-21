<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $shift = $request->input('shift');
        $keterangan = $request->input('keterangan');

        // Konversi bulan ke rentang tanggal
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        // Data absensi (hanya jika tidak memilih izin/cuti)
        // $absensi = Absensi::with('karyawan')
        //     ->when($shift, function ($query, $shift) {
        //         return $query->where('shift', $shift);
        //     })
        //     ->whereBetween('tanggal_scan', [$startDate, $endDate])
        //     ->get();

        // Data cuti
        $cuti = Cuti::with('karyawan')
            ->whereMonth('tanggal_mulai', Carbon::parse($bulan)->month)
            ->whereYear('tanggal_mulai', Carbon::parse($bulan)->year)
            ->get();

        // Data izin
        $izin = Izin::with('karyawan')
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->get();

        return view('penyelia.laporanPy', compact( 'cuti', 'izin', 'bulan', 'shift', 'keterangan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');
        $jenis = $request->input('jenis');

        if ($jenis === 'cuti') {
            $cuti = Cuti::findOrFail($id);
            $cuti->status = $status;
            $cuti->save();
        } elseif ($jenis === 'izin') {
            $izin = Izin::findOrFail($id);
            $izin->status = $status;
            $izin->save();
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }
}
