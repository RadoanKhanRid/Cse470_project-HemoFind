<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloodRequest; 
use App\Models\Donation; // 1. ADD THIS IMPORT
use Carbon\Carbon;

class BloodRequestController extends Controller
{
    public function index()
    {
        $requests = BloodRequest::all()->map(function($request) {
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

        return view('blood_requests.index', compact('requests'));
    }

    // 2. ADD THIS NEW FUNCTION
    public function findNearbyDonors($id)
    {
        // Find the specific blood request
        $request = BloodRequest::findOrFail($id);

        // Security check: If the request has no coordinates, we can't search
        if (!$request->lat || !$request->lng) {
            return back()->with('error', 'This hospital has no location data set.');
        }

        // Use the scope we added to the Donation model
        $nearbyDonors = Donation::where('blood_type', $request->blood_type)
            ->where('status', '!=', 'completed') // Optional: only show available donors
            ->withinDistance($request->lat, $request->lng, 5) // 5km radius
            ->orderBy('distance', 'asc')
            ->get();

        return view('blood_requests.nearby', compact('request', 'nearbyDonors'));
    }
    public function create()
    {
        return view('blood_requests.create');
    }

    public function store(Request $request)
{
    // 1. Debugging: Uncomment the line below to see what data is actually arriving
    // dd($request->all()); 

    $validated = $request->validate([
        'patient_name' => 'required|string',
        'blood_type' => 'required',
        'required_date' => 'required|date',
        'hospital_name' => 'required|string',
        'lat' => 'required',
        'lng' => 'required',
    ]);

    // 2. Using the 'new' method to be safe
    $newRequest = new \App\Models\BloodRequest();
    $newRequest->patient_name = $request->patient_name;
    $newRequest->blood_type = $request->blood_type;
    $newRequest->required_date = $request->required_date;
    $newRequest->hospital_name = $request->hospital_name;
    $newRequest->lat = $request->lat;
    $newRequest->lng = $request->lng;
    $newRequest->save();

    return redirect()->route('blood.requests')->with('success', 'Request saved with location!');
}
}