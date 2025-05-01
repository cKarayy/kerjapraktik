<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Penyelia extends Authenticatable
{
    protected $primaryKey = 'id_penyelia';
    protected $fillable = ['nama_lengkap', 'password_penyelia'];
    protected $hidden = ['password_penyelia'];

    public function getAuthPassword()
    {
        return $this->password_penyelia;
    }

}
