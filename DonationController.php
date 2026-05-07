<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\BloodRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function accept(BloodRequest $bloodRequest)
    {
        $user = Auth::user();

        // Check if donor already has an active donation
        $activeDonation = Donation::where('donor_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->first();

        if ($activeDonation) {
            return redirect()->back()->with('error', 'You already have an active help session. Please complete or cancel it first.');
        }

        $donation = Donation::create([
            'blood_request_id' => $bloodRequest->id,
            'donor_id' => $user->id,
            'status' => 'accepted',
            'requester_latitude' => $bloodRequest->latitude,
            'requester_longitude' => $bloodRequest->longitude,
            'live_latitude' => $user->latitude,
            'live_longitude' => $user->longitude,
        ]);

        return redirect()->route('donation.track', $donation);
    }

    public function cancel(Donation $donation)
    {
        // Only the donor can cancel
        if (Auth::id() !== $donation->donor_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $donation->update(['status' => 'cancelled']);

        return redirect()->route('dashboard')->with('success', 'Help session cancelled.');
    }

    public function track(Donation $donation)
    {
        // Security: only donor or requester can track
        if (Auth::id() !== $donation->donor_id && Auth::id() !== $donation->bloodRequest->user_id) {
            abort(403, 'Unauthorized access to tracking data.');
        }

        return view('donations.track', compact('donation'));
    }

    public function getData(Donation $donation)
    {
        if (Auth::id() !== $donation->donor_id && Auth::id() !== $donation->bloodRequest->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $donation->status,
            'live_latitude' => $donation->live_latitude,
            'live_longitude' => $donation->live_longitude,
            'requester_latitude' => $donation->requester_latitude,
            'requester_longitude' => $donation->requester_longitude,
        ]);
    }

    public function updateLocation(Request $request, Donation $donation)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        if ($user->id === $donation->donor_id) {
            $donation->update([
                'live_latitude' => $request->latitude,
                'live_longitude' => $request->longitude,
            ]);
        } else if ($user->id === $donation->bloodRequest->user_id) {
            $donation->update([
                'requester_latitude' => $request->latitude,
                'requester_longitude' => $request->longitude,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
