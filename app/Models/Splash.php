<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;


//ganti 1
class Splash extends Model
{
    use HasFactory;

    protected $table = 'splash_screen';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =
    [
        "id",
        "title",
        "image_url"
    ];
}
