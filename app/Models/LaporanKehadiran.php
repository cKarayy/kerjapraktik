<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanKehadiran extends Model
{
    use HasFactory;
    protected $table = 'laporan_kehadiran';
    protected $fillable = [
        'nama_pegawai',
        'kehadiran',
        'shift',
        'lateness',
        'tanggal_scan', 
        'bulan'
    ];
}
