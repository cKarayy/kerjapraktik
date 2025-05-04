<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $fillable = ['id_karyawan', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'status', 'id_penyelia','id_shift'];

    public function karyawan()
    {
        return $this->belongsTo(Employee::class, 'id_karyawan');
    }

    public function penyelia()
    {
        return $this->belongsTo(Penyelia::class, 'id_penyelia');
    }

    public function shift()
    {
        return $this->belongsTo(shifts::class, 'id_shift', 'id_shift');
    }
}
