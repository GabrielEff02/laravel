<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;


//ganti 1
class Requests extends Model
{
    use HasFactory;

    protected $table = 'requested_item';
    protected $primaryKey = 'REQUEST_ID';
    public $timestamps = false;

    //ganti 3
    protected $fillable =
    [
        "REQUEST_ID",
        "PRODUCT_NAME",
        "QUANTITY",
        "PRICE",
        "username",
        "REQUEST_DATE",
        "compan_code",
        "status",
    ];
}
