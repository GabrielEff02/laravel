<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jual extends Model
{
    use HasFactory;

    protected $table = 'jual';
    protected $primaryKey = 'transaction_id';
    public $timestamps = true;

    protected $fillable = [
        'transaction_id',
        'username',
        'transaction_date', // ini harus nama kolom asli di tabel
        'total_amount',
        'address',
        'is_delivery',
        'status',
        'driver_id',
        'compan_code',
    ];
}
