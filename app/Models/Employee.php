<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $table = 'karyawans';
    protected $primaryKey = 'id_karyawan';
    protected $fillable = ['id_shift', 'nama_lengkap', 'jabatan', 'password', 'status'];
    protected $hidden = ['password'];

    public function shift()
    {
        return $this->belongsTo(shifts::class, 'id_shift');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan');
    }
}
