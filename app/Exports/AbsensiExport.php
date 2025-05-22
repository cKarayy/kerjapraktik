<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $keterangan;

    public function __construct($data, $keterangan)
    {
        $this->data = $data;
        $this->keterangan = $keterangan;
    }

    public function collection()
    {
        // Sesuaikan data untuk export excel berdasar keterangan
        if ($this->keterangan == 'cuti') {
            return $this->data->map(function ($item) {
                return [
                    'Tanggal Mulai' => $item->tanggal_mulai,
                    'Tanggal Selesai' => $item->tanggal_selesai,
                    'Nama Lengkap' => $item->karyawan->nama_lengkap ?? '-',
                    'Alasan' => $item->alasan,
                    'Status' => ucfirst($item->status),
                ];
            });
        } elseif ($this->keterangan == 'izin') {
            return $this->data->map(function ($item) {
                return [
                    'Tanggal' => $item->tanggal,
                    'Nama Lengkap' => $item->karyawan->nama_lengkap ?? '-',
                    'Alasan' => $item->alasan,
                    'Status' => ucfirst($item->status),
                ];
            });
        } else {
            // absensi
            return $this->data->map(function ($item) {
                return [
                    'Tanggal' => $item->tanggal_scan,
                    'Nama Lengkap' => $item->karyawan->nama_lengkap ?? '-',
                    'Kehadiran' => $item->kehadiran,
                    'Keterangan' => $item->shift,
                    'Keterlambatan' => $item->lateness,
                ];
            });
        }
    }

    public function headings(): array
    {
        if ($this->keterangan == 'cuti') {
            return ['Tanggal Mulai', 'Tanggal Selesai', 'Nama Lengkap', 'Alasan', 'Status'];
        } elseif ($this->keterangan == 'izin') {
            return ['Tanggal', 'Nama Lengkap', 'Alasan', 'Status'];
        } else {
            return ['Tanggal', 'Nama Lengkap', 'Kehadiran', 'Keterangan', 'Keterlambatan'];
        }
    }
}
