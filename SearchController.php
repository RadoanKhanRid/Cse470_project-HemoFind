<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|max:100',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $bg = $request->blood_group;
        $radius = $request->radius ?? 5; // Default to 5km as requested

        // 1. Geospatial Radius Search using MySQL ST_Distance_Sphere (meters)
        $radiusInMeters = $radius * 1000;
        
        $allDonors = User::where('is_available', true)
            ->whereRaw("ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) <= ?", [$lng, $lat, $radiusInMeters])
            ->get();

        if (auth()->check()) {
            $allDonors = $allDonors->where('id', '!=', auth()->id());
        }

        // 2. Add Distance and Mock ETA (ETA API Integration Placeholder)
        $allDonors = $allDonors->map(function ($donor) use ($lat, $lng) {
            $donor->distance = $this->calculateDistance($lat, $lng, $donor->latitude, $donor->longitude);
            
            // ETA Calculation: In a real app, you would call Google Distance Matrix API here
            // For now, we estimate based on 30km/h average city speed
            $donor->eta_minutes = round(($donor->distance / 30) * 60) + 5; // +5 mins for prep
            
            return $donor;
        });

        // 3. Exact Match Routing (Priority 1)
        $exactMatches = $allDonors->where('blood_group', $bg)->sortBy('distance');
        
        // 4. Compatible Blood Group Fallback (Priority 2)
        $compatibleGroups = $this->getCompatibleGroups($bg);
        $compatibleGroups = array_diff($compatibleGroups, [$bg]); // Remove exact match
        $compatibleMatches = $allDonors->whereIn('blood_group', $compatibleGroups)->sortBy('distance');

        // 5. Universal Donor Fallback (Priority 3)
        $universalMatches = collect();
        if ($bg !== 'O-') {
            $universalMatches = $allDonors->where('blood_group', 'O-')
                ->whereNotIn('blood_group', array_merge([$bg], $compatibleGroups)) // Avoid duplicates
                ->sortBy('distance');
        }

        return view('search-results', compact('exactMatches', 'compatibleMatches', 'universalMatches', 'lat', 'lng', 'bg', 'radius'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // Convert to KM
    }


    private function getCompatibleGroups($bg)
    {
        $map = [
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'O+' => ['O+', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'AB+' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'O-' => ['O-'],
            'B-' => ['B-', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
        ];
        return $map[$bg] ?? [];
    }
}
