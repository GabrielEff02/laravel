<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;


//ganti 1
class Compan extends Model
{
    use HasFactory;

    protected $table = 'compan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =
    [
        "compan_code",
        "name",
        "address",
        "phone",
        "image_url",
        "code",
        "lat",
        "lang"
    ];
}
