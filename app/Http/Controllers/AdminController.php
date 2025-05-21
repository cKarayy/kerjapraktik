<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Penyelia;
use App\Models\Employee;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Exports\LaporanExport;
use App\Models\Cuti;
use App\Models\Izin;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function showLaporan(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $shift = $request->input('shift');
        $keterangan = $request->input('keterangan');

        // Ambil awal dan akhir bulan
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth();

        $laporan = collect(); // Gunakan collection kosong sebagai default

        // Ambil laporan berdasarkan keterangan (izin/cuti), atau absensi default
        if ($keterangan === 'izin') {
            $laporan = Izin::with('karyawan', 'shift')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($shift, function ($query, $shift) {
                    return $query->whereHas('shift', fn($q) => $q->where('nama_shift', $shift));
                })
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'tanggal_scan' => $item->tanggal,
                        'nama_pegawai' => $item->karyawan->nama,
                        'kehadiran' => 'Izin',
                        'keterangan' => optional($item->shift)->nama_shift ?? '-',
                        'keterlambatan' => '-',
                    ];
                });
        } elseif ($keterangan === 'cuti') {
            $laporan = Cuti::with('karyawan', 'shift')
                ->whereBetween('tanggal_mulai', [$startDate, $endDate])
                ->when($shift, function ($query, $shift) {
                    return $query->whereHas('shift', fn($q) => $q->where('nama_shift', $shift));
                })
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'tanggal_scan' => $item->tanggal_mulai . ' s/d ' . $item->tanggal_selesai,
                        'nama_pegawai' => $item->karyawan->nama,
                        'kehadiran' => 'Cuti',
                        'keterangan' => optional($item->shift)->nama_shift ?? '-',
                        'keterlambatan' => '-',
                    ];
                });
        } else {
            $laporan = Absensi::with('karyawan', 'shift')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($shift, function ($query, $shift) {
                    return $query->whereHas('shift', fn($q) => $q->where('nama_shift', $shift));
                })
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'tanggal_scan' => $item->tanggal,
                        'nama_pegawai' => $item->karyawan->nama,
                        'kehadiran' => $item->kehadiran,
                        'keterangan' => optional($item->shift)->nama_shift ?? '-',
                        'keterlambatan' => gmdate('H:i:s', $item->keterlambatan ?? 0),
                    ];
                });
        }

        return view('admin.laporan', compact(var_name: 'laporan'));
    }


    // public function exportLaporan(Request $request)
    // {
    //     $bulan = $request->input('bulan');
    //     $shift = $request->input('shift');

    //     return Excel::download(new LaporanExport($bulan, $shift), 'laporan_kehadiran.xlsx');
    // }


    // Dashboard penyelia
    public function penyeliaDashboard()
    {
        return view('penyelia.db');
    }

    // Dashboard admin
    public function admDashboard()
    {
        return view('admin.dashboard');
    }

    public function pgDashboard()
    {
        return view(view: 'pegawai.home');
    }
}

