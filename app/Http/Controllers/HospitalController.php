<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalController extends Controller
{
    // Shows the list and handles the Search bar
    public function index(Request $request)
{
    $query = Donation::query();

    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        
        $query->where(function($q) use ($searchTerm) {
            $q->where('donor_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('blood_type', 'like', '%' . $searchTerm . '%');
        });
    }

    $donations = $query->get();
    return view('hospital.dashboard', compact('donations'));
}

    // Step 1: Donor Check-in
    // Step 1: Mark as Arrived
public function checkIn($id)
{
    $donation = Donation::findOrFail($id);
    $donation->update([
        'status' => 'Arrived', // Capital 'A'
        'arrived_at' => now()
    ]);
    return back();
}

public function verify($id)
{
    $donation = Donation::findOrFail($id);
    $donation->update([
        'status' => 'Completed', // Capital 'C'
        'verified_at' => now()
    ]);
    return back();
}

    // Generate PDF
    public function downloadCertificate($id)
    {
        $donation = Donation::findOrFail($id);
        $pdf = Pdf::loadView('pdf.certificate', compact('donation'));
        return $pdf->download('Certificate_'.$donation->donor_name.'.pdf');
    }
   public function create()
{
    // This opens the map form we made earlier
    return view('hospital.create2');
}

public function store(Request $request)
{
    // 1. Basic Validation
    // This checks for required fields and handles numerical limits for Age and Weight
    $request->validate([
        'donor_name' => 'required|string|max:255',
        'blood_type' => 'required',
        'phone'      => 'required|string',
        'email'      => 'required|email',
        'lat'        => 'required',
        'lng'        => 'required',
        'age'        => 'required|numeric|min:18|max:65',
        'weight'     => 'required|numeric|min:50',
    ]);

    // 2. Medical Eligibility Logic (The "Gatekeeper")
    // Check for Tattoos/Surgery
    if ($request->has_recent_tattoo_surgery == '1') {
        return back()->withInput()->with('error', 'Registration Failed: Donors must wait 6 months after a tattoo or surgery.');
    }

    // Check for Chronic Conditions
    if ($request->has_chronic_condition == '1') {
        return back()->withInput()->with('error', 'Registration Failed: Donor is ineligible due to chronic medical conditions.');
    }

    // 3. Saving to Database
    // Only if the code reaches this point has the donor passed all medical checks
    \App\Models\Donation::create([
        'donor_name' => $request->donor_name,
        'blood_type' => $request->blood_type,
        'phone'      => $request->phone,
        'email'      => $request->email,
        'age'        => $request->age,
        'weight'     => $request->weight,
        'lat'        => $request->lat,
        'lng'        => $request->lng,
        'status'     => 'pending', // Default status
    ]);
    if ($request->has_recent_tattoo_surgery == '1') {
    return back()->withInput()->with('error', 'Registration Failed: Donors must wait 6 months after a tattoo or surgery.');
}

    // 4. Redirect with Success Message
    return redirect()->route('hospital.index')->with('success', 'Medical verification passed! Donor registered successfully.');
}
}