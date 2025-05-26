<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'driver_id';
    public $timestamps = true;

    //ganti 3
    protected $fillable =
    [
        "username",
        "driver_id",
        "driver_name",
        "address",
        "phone",
        "license_number",
        "email",
        'password',
        "status",
        "manager",
    ];
    protected $hidden = [
        'password',
    ];
}
