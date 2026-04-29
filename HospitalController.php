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
    $request->validate([
        'donor_name' => 'required',
        'blood_type' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    ]);

    // Create the donor record
    \App\Models\Donation::create([
        'donor_name' => $request->donor_name,
        'blood_type' => $request->blood_type,
        'status' => 'pending',
        'lat' => $request->lat,
        'lng' => $request->lng,
    ]);

    // Go back to the hospital list
    return redirect()->route('hospital.index')->with('success', 'Donor added successfully!');
}
}