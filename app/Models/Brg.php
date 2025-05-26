<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;


//ganti 1
class Brg extends Model
{
    use HasFactory;

    protected $table = 'brg';
    protected $primaryKey = 'brg_id';
    public $timestamps = true;

    //ganti 3
    protected $fillable =
    [
        "brg_id",
        "nama",
        "harga",
        "satuan",
        "deskripsi",
        "url",
        "category_id",
    ];
}
