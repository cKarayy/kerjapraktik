<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QR extends Model
{
    protected $fillable = ['code', 'valid_until', 'is_used'];
}
