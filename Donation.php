<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donor_name',
        'blood_type',
        'status',
        'arrived_at',
        'verified_at',
        'lat', // Added for location
        'lng', // Added for location
    ];

    /**
     * Scope a query to only include donors within a given distance.
     * 6371 is the Earth's radius in kilometers.
     */
    public function scopeWithinDistance($query, $latitude, $longitude, $radius = 5)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) 
                        * cos(radians(lat)) 
                        * cos(radians(lng) - radians($longitude)) 
                        + sin(radians($latitude)) 
                        * sin(radians(lat))))";

        return $query->select('*')
                     ->selectRaw("$haversine AS distance")
                     ->whereRaw("$haversine <= ?", [$radius]);
    }
}