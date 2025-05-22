<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'id_karyawan',
        'id_admin',
        'tanggal',
        'waktu_masuk',
        'kehadiran',
        'id_shift',
        'keterlambatan',
        'id_code',
        'bukti_foto' 
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function shift()
    {
        return $this->belongsTo(shifts::class, 'id_shift', 'id_shift');
    }

    public function karyawan()
    {
        return $this->belongsTo(Employee::class, 'id_karyawan');
    }

    public function qrCode()
    {
        return $this->belongsTo(QR::class, 'id_code');
    }
}
