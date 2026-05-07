<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
    protected $table = 'blood_inventory';

    protected $fillable = [
        'donation_id',
        'donor_name',
        'blood_type',
        'hospital_name',
        'bag_number',
        'collection_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'collection_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];
}