<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_name',
        'blood_group',
        'hospital_name',
        'area',
        'latitude',
        'longitude',
        'urgency',
        'contact_number',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = ['effective_blood_group'];

    public function getEffectiveBloodGroupAttribute()
    {
        if ($this->created_at->diffInMinutes(now()) >= 10 && $this->blood_group !== 'O-') {
            return 'O-';
        }
        return $this->blood_group;
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function latestDonation()
    {
        return $this->hasOne(Donation::class)->latestOfMany();
    }

    public function isFallbackActive()
    {
        return $this->created_at->diffInMinutes(now()) >= 10;
    }
}
