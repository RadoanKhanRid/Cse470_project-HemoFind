<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'live_latitude',
        'live_longitude',
        'requester_latitude',
        'requester_longitude',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Calculate distance between donor and requester in km
     */
    public function getDistanceAttribute()
    {
        if (!$this->live_latitude || !$this->requester_latitude) return null;
        
        $lat1 = $this->live_latitude;
        $lon1 = $this->live_longitude;
        $lat2 = $this->requester_latitude;
        $lon2 = $this->requester_longitude;
        
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return round($distance, 2);
    }
}
