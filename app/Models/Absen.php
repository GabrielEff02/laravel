<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absen';
    public $timestamps = false;

    protected $fillable = [
        'kodep',
        'tgl'
    ];
}
