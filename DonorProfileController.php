<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonorProfileController extends Controller
{
    public function show()
    {
        return view('donor.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'blood_group' => 'required|string',
            'area' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_available' => 'boolean',
            'last_donated_at' => 'nullable|date',
        ]);

        $user->update(array_merge($validated, ['role' => 'donor']));

        return redirect()->route('dashboard')->with('success', 'Donor profile updated successfully!');
    }
}
