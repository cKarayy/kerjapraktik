<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\shifts;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;

class LaporanController extends Controller
{
  public function index(Request $request)
{
    $bulan = $request->input('bulan', date('Y-m'));
    $idShift = $request->input('shift'); // ini id_shift dari input
    $keterangan = $request->input('keterangan');

    $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
    $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

    $shifts = shifts::all();

    // Ambil nama shift berdasarkan idShift yang diberikan (kalau ada)
    $shiftName = null;
    if ($idShift) {
        $shiftRecord = shifts::find($idShift);
        $shiftName = $shiftRecord ? $shiftRecord->nama_shift : null;
    }

    $absensi = collect();
    $cuti = collect();
    $izin = collect();

    if (!$keterangan || $keterangan == 'hadir') {
        // $absensi = Absensi::with('karyawan')
        //     ->whereBetween('tanggal_scan', [$startDate, $endDate])
        //     ->when($idShift, function ($query) use ($idShift) {
        //         return $query->where('id_shift', $idShift);
        //     })
        //     ->get();
    } elseif ($keterangan == 'cuti') {
        $cuti = Cuti::with('karyawan')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                      ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
            })
            ->when($idShift, function ($query) use ($idShift) {
                return $query->where('id_shift', $idShift);
            })
            ->get();
    } elseif ($keterangan == 'izin') {
        $izin = Izin::with('karyawan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when($idShift, function ($query) use ($idShift) {
                return $query->where('id_shift', $idShift);
            })
            ->get();
    }

    return view('penyelia.laporanPy', compact('absensi', 'cuti', 'izin', 'bulan', 'shiftName', 'keterangan', 'shifts'));
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

    public function export(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $shiftName = $request->input('shift');
        $keterangan = $request->input('keterangan');
        $format = $request->input('format'); // 'pdf' atau 'excel'

        // Hitung tanggal awal dan akhir bulan
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        // Cari id shift berdasarkan nama shift
        $idShift = null;
        if ($shiftName) {
            $shiftRecord = shifts::whereRaw('LOWER(nama_shift) = ?', [strtolower($shiftName)])->first();
            if ($shiftRecord) {
                $idShift = $shiftRecord->id;
            }
        }

        // Ambil data sesuai keterangan
        if (!$keterangan || $keterangan == 'hadir') {
            $data = Absensi::with('karyawan')
                ->whereBetween('tanggal_scan', [$startDate, $endDate])
                ->when($idShift, fn($q) => $q->where('id_shift', $idShift))
                ->get();
        } elseif ($keterangan == 'cuti') {
            $data = Cuti::with('karyawan')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
                })
                ->when($idShift, fn($q) => $q->where('id_shift', $idShift))
                ->get();
        } elseif ($keterangan == 'izin') {
            $data = Izin::with('karyawan')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($idShift, fn($q) => $q->where('id_shift', $idShift))
                ->get();
        } else {
            $data = collect();
        }

        if ($format == 'pdf') {
            $pdf = PDF::loadView('penyelia.laporan_pdf', [
                'data' => $data,
                'keterangan' => $keterangan,
                'bulan' => $bulan,
                'shiftName' => $shiftName
            ]);
            return $pdf->download('laporan_kehadiran_'.$bulan.'.pdf');
        } elseif ($format == 'excel') {
            return Excel::download(new AbsensiExport($data, $keterangan), 'laporan_kehadiran_'.$bulan.'.xlsx');
        }

        return redirect()->back()->with('error', 'Format export tidak valid.');
    }

      public function indexAdmin(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $idShift = $request->input('shift'); // ini id_shift dari input
        $keterangan = $request->input('keterangan');

        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        $shifts = shifts::all();

        // Ambil nama shift berdasarkan idShift yang diberikan (kalau ada)
        $shiftName = null;
        if ($idShift) {
            $shiftRecord = shifts::find($idShift);
            $shiftName = $shiftRecord ? $shiftRecord->nama_shift : null;
        }

        $absensi = collect();
        $cuti = collect();
        $izin = collect();

        if (!$keterangan || $keterangan == 'hadir') {
            // $absensi = Absensi::with('karyawan')
            //     ->whereBetween('tanggal_scan', [$startDate, $endDate])
            //     ->when($idShift, function ($query) use ($idShift) {
            //         return $query->where('id_shift', $idShift);
            //     })
            //     ->get();
        } elseif ($keterangan == 'cuti') {
            $cuti = Cuti::with('karyawan')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                        ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
                })
                ->when($idShift, function ($query) use ($idShift) {
                    return $query->where('id_shift', $idShift);
                })
                ->get();
        } elseif ($keterangan == 'izin') {
            $izin = Izin::with('karyawan')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($idShift, function ($query) use ($idShift) {
                    return $query->where('id_shift', $idShift);
                })
                ->get();
        }

        return view('admin.laporan', compact('absensi', 'cuti', 'izin', 'bulan', 'shiftName', 'keterangan', 'shifts'));
    }
}
