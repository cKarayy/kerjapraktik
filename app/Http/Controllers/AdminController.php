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
    // Fungsi untuk mendaftarkan admin atau penyelia
    // Tampilkan form register
    public function showRegister()
    {
        return view('admin.register');
    }

    // Proses register
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,penyelia',
        ]);

        $namaLengkap = $request->full_name;

        if ($request->role === 'admin') {
            Admin::create([
                'nama_lengkap' => $namaLengkap,
                'password_admin' => Hash::make($request->password),
            ]);
        } else {
            if (Penyelia::count() >= 2) {
                return redirect()->back()->with('error', 'Maksimal penyelia hanya 2 orang.');
            }

            Penyelia::create([
                'nama_lengkap' => $namaLengkap,
                'password_penyelia' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

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

        return view('admin.laporan', compact('laporan'));
    }


    // public function exportLaporan(Request $request)
    // {
    //     $bulan = $request->input('bulan');
    //     $shift = $request->input('shift');

    //     return Excel::download(new LaporanExport($bulan, $shift), 'laporan_kehadiran.xlsx');
    // }

    public function updateStatus($id, Request $request)
    {
        $status = $request->input('status_persetujuan');
        $izinCuti = Izin::find($id) ?: Cuti::find($id);

        if ($izinCuti) {
            $izinCuti->status = $status;
            $izinCuti->save();
        }

        return redirect()->back();
    }


    // Tampilkan form login
    public function showLoginForm()
    {
        return view('admin.login');
    }


}
