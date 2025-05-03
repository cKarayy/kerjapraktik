<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    protected $fillable = ['id_karyawan', 'tanggal', 'alasan', 'status', 'id_penyelia'];

    public function karyawan()
    {
        return $this->belongsTo(Employee::class, 'id_karyawan');
    }

    public function penyelia()
    {
        return $this->belongsTo(Penyelia::class, 'id_penyelia');
    }
}
