<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_id',
        'hospital_name',
        'hospital_location',
        'blood_group',
        'donated_at',
    ];

    protected $casts = [
        'donated_at' => 'date',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
}
