<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan kehadiran pegawai.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->toDateString());

        // Mengambil data absensi
        $absensi = Absensi::with(['karyawan', 'admin', 'shift'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get();

        // Mengambil data cuti
        $cuti = Cuti::with('karyawan')
            ->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
            ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
            ->get();

        // Mengambil data izin
        $izin = Izin::with('karyawan')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get();

        // Menggabungkan data absensi, cuti, dan izin menjadi satu koleksi
        $laporan = $absensi->merge($cuti)->merge($izin);

        // Mengirim data ke view laporan
        return view('admin.laporan', [
            'laporan' => $laporan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ]);
    }


    public function showLaporan(Request $request)
    {
        // Ambil data berdasarkan filter yang diterima
        $bulan = $request->input('bulan', date('Y-m'));
        $shift = $request->input('shift');
        $keterangan = $request->input('keterangan');

        // Mengambil data dari tabel absensi, cuti, dan izin
        $laporan = Absensi::with('shift', 'karyawan')
            ->whereMonth('tanggal', '=', $bulan)
            ->when($shift, function ($query, $shift) {
                return $query->where('id_shift', $shift);  // Pastikan Anda menggunakan id_shift yang tepat
            })
            ->when($keterangan, function ($query, $keterangan) {
                return $query->where('kehadiran', $keterangan);  // Menggunakan kolom kehadiran
            })
            ->get();

        // Kirim data laporan ke view
        return view('admin.laporan', compact('laporan'));
    }


}
