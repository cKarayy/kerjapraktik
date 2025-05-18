<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable
{
    protected $table = 'karyawans';
    protected $primaryKey = 'id_karyawan';
    protected $fillable = ['id_shift', 'nama_lengkap', 'jabatan', 'password', 'status', 'username', 'role'];
    protected $hidden = ['password'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($karyawan) {
            // Menetapkan role berdasarkan kode di username
            if (str_ends_with($karyawan->username, '1')) {
                $karyawan->role = 'admin';
            } elseif (str_ends_with($karyawan->username, '2')) {
                $karyawan->role = 'pegawai';
            } else {
                // Default role jika tidak ada kode yang sesuai
                $karyawan->role = 'pegawai';
            }
        });
    }

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
    public function getAuthPassword()
    {
        return $this->attributes['password']; // Menggunakan kolom 'password' untuk otentikasi
    }


}
