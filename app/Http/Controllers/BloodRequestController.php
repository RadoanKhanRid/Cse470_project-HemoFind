<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloodRequest; 
use App\Models\Donation; 
use Carbon\Carbon;

class BloodRequestController extends Controller
{
    public function index()
    {
        // 1. We only get 'pending' requests
        // 2. We apply the color/priority logic
        $requests = BloodRequest::where('status', 'pending')
            ->get()
            ->map(function($request) {
                $deadline = Carbon::parse($request->required_date);
                $daysLeft = now()->startOfDay()->diffInDays($deadline, false);
                
                if ($daysLeft <= 1) {
                    $request->priority = 'Urgent (Within 24h)';
                    $request->color = 'red';
                } elseif ($daysLeft <= 4) {
                    $request->priority = 'High (Soon)';
                    $request->color = 'yellow';
                } else {
                    $request->priority = 'Normal';
                    $request->color = 'green';
                }
                
                return $request;
            })->sortBy('required_date');

        // Note: Ensure this path matches your view location
        return view('blood_requests.index', compact('requests'));
    }

    public function complete($id)
    {
        $request = BloodRequest::findOrFail($id);
        $request->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Request marked as completed and removed from view.');
    }

    public function findNearbyDonors($id)
    {
        $request = BloodRequest::findOrFail($id);

        if (!$request->lat || !$request->lng) {
            return back()->with('error', 'This hospital has no location data set.');
        }

        $nearbyDonors = Donation::where('blood_type', $request->blood_type)
            ->where('status', '!=', 'completed')
            ->withinDistance($request->lat, $request->lng, 5) 
            ->orderBy('distance', 'asc')
            ->get();

        return view('blood_requests.nearby', compact('request', 'nearbyDonors'));
    }

   public function donorCoverage(Request $request)
{
    $selectedGroup = $request->query('blood_group');

    // 1. Get all donors
    $donorQuery = \App\Models\Donation::query();
    if ($selectedGroup) {
        $donorQuery->where('blood_type', $selectedGroup);
    }
    $donors = $donorQuery->get();

    // 2. Get unique hospitals
    $hospitals = \App\Models\BloodRequest::select('hospital_name', 'lat', 'lng')
        ->whereNotNull('lat')->whereNotNull('lng')
        ->groupBy('hospital_name', 'lat', 'lng')
        ->get();

    $counts = [];
    foreach ($hospitals as $h) {
        $counts[$h->hospital_name] = 0;
    }

    // 3. The Logic: Assign donor to the SINGLE nearest hospital
    foreach ($donors as $donor) {
        $closestHospital = null;
        $minDistance = 999999;

        foreach ($hospitals as $hospital) {
            // Corrected PHP Haversine math using deg2rad
            $lat1 = deg2rad((float)$donor->lat);
            $lng1 = deg2rad((float)$donor->lng);
            $lat2 = deg2rad((float)$hospital->lat);
            $lng2 = deg2rad((float)$hospital->lng);

            $dist = 6371 * acos(
                cos($lat1) * cos($lat2) * cos($lng2 - $lng1) + 
                sin($lat1) * sin($lat2)
            );

            if ($dist < $minDistance) {
                $minDistance = $dist;
                $closestHospital = $hospital->hospital_name;
            }
        }

        // Only count the donor if they are within 5km of their absolute nearest hospital
        if ($closestHospital && $minDistance <= 5) {
            $counts[$closestHospital]++;
        }
    }

    $coverage = [];
    foreach ($hospitals as $hospital) {
        $coverage[] = (object) [
            'hospital_name' => $hospital->hospital_name,
            'total_donors'  => $counts[$hospital->hospital_name] ?? 0,
        ];
    }

    $coverage = collect($coverage)->sortBy('total_donors');

    return view('hospital.donor_coverage', compact('coverage', 'selectedGroup'));
}
    public function create()
    {
        return view('blood_requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string',
            'blood_type' => 'required',
            'required_date' => 'required|date',
            'hospital_name' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $newRequest = new BloodRequest();
        $newRequest->patient_name = $request->patient_name;
        $newRequest->blood_type = $request->blood_type;
        $newRequest->required_date = $request->required_date;
        $newRequest->hospital_name = $request->hospital_name;
        $newRequest->lat = $request->lat;
        $newRequest->lng = $request->lng;
        $newRequest->status = 'pending'; // Ensure new requests start as pending
        $newRequest->save();

        return redirect()->route('blood.requests')->with('success', 'Request saved with location!');
    }
}