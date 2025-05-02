<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QR extends Model
{
    protected $table = 'qr_codes';
    protected $primaryKey = 'id_code';
    protected $fillable = ['id_admin', 'kode', 'waktu_generate', 'kehadiran'];

    protected $dates = [
        'waktu_generate',
        'created_at',
        'updated_at'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
}
