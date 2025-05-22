<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Penyelia extends Authenticatable
{
    protected $primaryKey = 'id_penyelia';
    protected $fillable = ['nama_lengkap', 'password'];
    protected $hidden = ['password'];

    public function getAuthPassword()
    {
        return $this->sendPasswordResetNotification;
    }

}
