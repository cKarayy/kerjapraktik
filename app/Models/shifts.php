<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shifts extends Model
{
    protected $primaryKey = 'id_shift';
    protected $fillable = ['nama_shift', 'jam_masuk', 'jam_keluar'];
}
