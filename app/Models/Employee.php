<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable
{
    protected $table = 'karyawans';
    protected $primaryKey = 'id_karyawan';
    protected $fillable = ['id_shift', 'nama_lengkap', 'jabatan', 'password', 'status', 'username', 'role'];
    protected $hidden = ['password'];
     protected $casts = ['password' => 'hashed'];

    public function shift()
    {
        return $this->belongsTo(shifts::class, 'id_shift');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan');
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class, 'id_karyawan');
    }

    public function izins()
    {
        return $this->hasMany(Izin::class, 'id_karyawan');
    }
}
