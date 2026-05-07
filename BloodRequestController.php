<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requests = BloodRequest::with('latestDonation')->latest()->get();

        if ($user && $user->latitude && $user->longitude) {
            foreach ($requests as $request) {
                if ($request->latitude && $request->longitude) {
                    $request->distance = $this->calculateDistance($user->latitude, $user->longitude, $request->latitude, $request->longitude);
                }
            }
        }

        return view('requests.index', compact('requests'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c, 2);
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'blood_group' => 'required|string',
            'hospital_name' => 'required|string|max:255',
            'area' => 'required|string',
            'contact_number' => 'required|string',
            'urgency' => 'required|string',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        BloodRequest::create(array_merge($validated, [
            'user_id' => auth()->id()
        ]));

        return redirect()->route('requests.index')->with('success', 'Blood request posted successfully!');
    }

    public function destroy(BloodRequest $bloodRequest)
    {
        if ($bloodRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $bloodRequest->delete();
        return redirect()->route('requests.index')->with('success', 'Post deleted successfully!');
    }
}
