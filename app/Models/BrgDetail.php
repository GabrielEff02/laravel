<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrgDetail extends Model
{
    use HasFactory;

    protected $table = 'brgd';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable =
    [
        "compan_code",
        "brg_id",
        "quantity"
    ];
}
