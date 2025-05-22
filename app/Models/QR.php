<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QR extends Model
{
    protected $table = 'qr_code';
    protected $primaryKey = 'id_code';
     protected $fillable = [
        'id_admin',
        'kode',
        'id_shift',
        'nama_lokasi',  // Jika sudah ditambahkan
        'latitude',     // Jika sudah ditambahkan
        'longitude'     // Jika sudah ditambahkan
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function shift()
    {
        return $this->belongsTo(shifts::class, 'id_shift', 'id_shift');
    }

     public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_code', 'id_code');
    }
}
