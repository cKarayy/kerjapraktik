<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'id_admin';

     protected $fillable = ['username', 'nama_lengkap', 'password'];

    protected $hidden = ['password'];

    public function getAuthPassword()
    {
        return $this->password;
    }
}
