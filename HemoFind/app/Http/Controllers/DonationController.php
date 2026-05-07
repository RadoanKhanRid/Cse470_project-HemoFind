<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::where('donor_id', Auth::id())
            ->latest()
            ->get();

        return view('donations.index', compact('donations'));
    }

    public function create()
    {
        return view('donations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_name' => 'required|string|max:500',
            'hospital_location' => 'nullable|string|max:500',
            'blood_group' => 'required|string|max:50',
            'other_blood_group' => 'nullable|string|max:100',
            'donated_at' => 'required|date',
        ]);

        if ($validated['blood_group'] === 'Other') {
            $validated['blood_group'] = $validated['other_blood_group'];
        }

        unset($validated['other_blood_group']);

        $validated['donor_id'] = Auth::id();

        Donation::create($validated);

        return redirect()
            ->route('donations.index')
            ->with('Success!', 'Donation recorded successfully.');
    }
}
