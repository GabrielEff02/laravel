<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;


//ganti 1
class Carousel extends Model
{
    use HasFactory;

    protected $table = 'carousel_images';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =
    [
        "id",
        "image_url",
        "title"
    ];
}
