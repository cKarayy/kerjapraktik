<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $primaryKey = 'id_absensi';
    protected $fillable = ['id_karyawan', 'id_admin', 'tanggal', 'waktu_masuk', 'waktu_keluar', 'kehadiran'];


    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
}

