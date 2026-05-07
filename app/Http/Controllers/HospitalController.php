<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use App\Models\BloodInventory;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalController extends Controller
{
    /**
     * Display the donation list with optional search filtering.
     */
    public function index(Request $request)
    {
        $query = Donation::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('donor_name', 'like', "%{$searchTerm}%")
                  ->orWhere('blood_type', 'like', "%{$searchTerm}%");
            });
        }

        $donations = $query->get();

        return view('hospital.dashboard', compact('donations'));
    }

    /**
     * Step 1: Mark donation as Arrived.
     */
    public function checkIn(int $id)
    {
        $donation = Donation::findOrFail($id);

        $donation->update([
            'status'     => 'Arrived',
            'arrived_at' => now(),
        ]);

        return back();
    }

    /**
     * Step 2: Mark donation as Completed, start 120-day cooldown,
     * and update the donor's trust score.
     */
    public function verify($id)
    {
        $donation = Donation::findOrFail($id);

        $donation->update([
            'status' => 'Completed',
            'verified_at' => now(),
            'cooldown_until' => now()->addDays(120),
        ]);

        $existingBag = BloodInventory::where('donation_id', $donation->id)->first();

        if (!$existingBag) {
            BloodInventory::create([
                'donation_id' => $donation->id,
                'donor_name' => $donation->donor_name,
                'blood_type' => $donation->blood_type,
                'hospital_name' => $donation->hospital_name ?? null,
                'bag_number' => 'BAG-' . now()->format('YmdHis') . '-' . $donation->id,
                'collection_date' => now(),
                'expiry_date' => now()->addDays(42),
                'status' => 'Available',
            ]);
        }

        if (method_exists($this, 'updateDonorTrust')) {
            $this->updateDonorTrust($donation->fresh());
        }

        return back()->with(
            'success',
            'Donation completed. Blood bag added to inventory and cooldown started.'
        );
    }

    /**
     * Generate and download a PDF certificate for a donation.
     */
    public function downloadCertificate(int $id)
    {
        $donation = Donation::findOrFail($id);

        $pdf = Pdf::loadView('pdf.certificate', compact('donation'));

        return $pdf->download("Certificate_{$donation->donor_name}.pdf");
    }

    /**
     * Show the donor registration form.
     */
    public function create()
    {
        return view('hospital.create2');
    }

    /**
     * Validate, check eligibility, and store a new donation.
     */
    public function store(Request $request)
    {
        // 1. Basic validation
        $request->validate([
            'donor_name' => 'required|string|max:255',
            'hospital_name' => 'required|string|max:255',
            'blood_type' => 'required',
            'phone'      => 'required|string',
            'email'      => 'required|email',
            'lat'        => 'required',
            'lng'        => 'required',
            'age'        => 'required|numeric|min:18|max:65',
            'weight'     => 'required|numeric|min:50',
        ]);

        // 2. Block donor if still within 120-day cooldown
        $existingDonor = Donation::where(function ($query) use ($request) {
                $query->where('email', $request->email)
                      ->orWhere('phone', $request->phone);
            })
            ->whereNotNull('cooldown_until')
            ->where('cooldown_until', '>', now())
            ->latest()
            ->first();

        if ($existingDonor) {
            $cooldownDate = $existingDonor->cooldown_until->format('d M Y h:i A');

            return back()->withInput()->with(
                'error',
                "Registration Failed: This donor is still in cooldown until {$cooldownDate}."
            );
        }

        // 3. Medical eligibility checks
        if ($request->has_recent_tattoo_surgery == '1') {
            return back()->withInput()->with(
                'error',
                'Registration Failed: Donors must wait 6 months after a tattoo or surgery.'
            );
        }

        if ($request->has_chronic_condition == '1') {
            return back()->withInput()->with(
                'error',
                'Registration Failed: Donor is ineligible due to chronic medical conditions.'
            );
        }

        // 4. Save to database
        Donation::create([
            'donor_name' => $request->donor_name,
            'blood_type' => $request->blood_type,
            'hospital_name' => $request->hospital_name,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'age'        => $request->age,
            'weight'     => $request->weight,
            'lat'        => $request->lat,
            'lng'        => $request->lng,
            'status'     => 'pending',
        ]);

        // 5. Redirect with success message
        return redirect()
            ->route('hospital.index')
            ->with('success', 'Medical verification passed! Donor registered successfully.');
    }

    /**
     * Recalculate and update the trust tier, score, and hospital badges for a donor.
     */
    private function updateDonorTrust(Donation $donation): void
    {
        $donorQuery = Donation::where(function ($query) use ($donation) {
            $query->where('email', $donation->email)
                  ->orWhere('phone', $donation->phone);
        });

        $completedDonations = (clone $donorQuery)
            ->where('status', 'Completed')
            ->count();

        $uniqueHospitalsVisited = (clone $donorQuery)
            ->where('status', 'Completed')
            ->whereNotNull('hospital_name')
            ->distinct('hospital_name')
            ->count('hospital_name');

        // Determine trust tier based on completed donations
        [$trustTier, $tierScore] = match (true) {
            $completedDonations >= 100 => ['Diamond',  10],
            $completedDonations >= 50  => ['Platinum',  8],
            $completedDonations >= 20  => ['Gold',       6],
            $completedDonations >= 10  => ['Silver',     4],
            $completedDonations >= 5   => ['Bronze',     2],
            default                    => ['New Donor',  0],
        };

        // Determine hospital badge and bonus score
        $hospitalScore = 0;
        $hospitalBadge = null;

        if ($uniqueHospitalsVisited >= 10) {
            $hospitalScore = floor($uniqueHospitalsVisited / 10) + 1;
            $hospitalBadge = "{$uniqueHospitalsVisited} Hospitals Visited";
        } elseif ($uniqueHospitalsVisited >= 1) {
            $hospitalScore = 1;
            $hospitalBadge = 'First Hospital Visited';
        }

        $totalTrustScore = $tierScore + $hospitalScore;

        (clone $donorQuery)->update([
            'trust_score'             => $totalTrustScore,
            'trust_tier'              => $trustTier,
            'hospitals_visited_count' => $uniqueHospitalsVisited,
            'hospitals_visited_badge' => $hospitalBadge,
        ]);
    }
}