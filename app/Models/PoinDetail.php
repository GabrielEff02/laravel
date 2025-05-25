<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoinDetail extends Model
{
    use HasFactory;

    protected $table = 'productd';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable =
    [
        "compan_code",
        "product_id",
        "quantity"
    ];
}
