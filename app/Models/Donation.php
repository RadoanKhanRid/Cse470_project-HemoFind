<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_name',
        'blood_type',
        'hospital_name',
        'phone',
        'email',
        'age',
        'weight',
        'lat',
        'lng',
        'status',
        'arrived_at',
        'verified_at',
        'cooldown_until',

        // TRUST SYSTEM
        'trust_score',
        'trust_tier',

        // HOSPITAL BADGES
        'hospitals_visited_count',
        'hospitals_visited_badge',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'verified_at' => 'datetime',
        'cooldown_until' => 'datetime',
    ];
}