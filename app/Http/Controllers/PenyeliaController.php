<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Employee;
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

    public function showEmployeeData()
    {
        // Mengambil data pegawai dan mengelompokkan berdasarkan role
        $adminEmployees = Employee::where('role', 'admin')->get();
        $regularEmployees = Employee::where('role', 'pegawai')->get();

        // Mengirim data ke view
        return view('pegawai.index', compact('adminEmployees', 'regularEmployees'));
    }
}
