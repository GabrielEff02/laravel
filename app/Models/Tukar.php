<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tukar extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    public $timestamps = true;

    protected $fillable = [
        'transaction_id',
        'username',
        'transaction_date',
        'total_amount',
        'address',
        'is_delivery',
        'status',
        'driver_id',
        'compan_code',
    ];
}
