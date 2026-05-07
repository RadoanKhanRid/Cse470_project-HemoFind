<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use Illuminate\Http\Request;

class BloodInventoryController extends Controller
{
    public function index()
    {
        BloodInventory::where('status', 'Available')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->update(['status' => 'Expired']);

        $summary = BloodInventory::selectRaw("
                blood_type,
                SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_units,
                SUM(CASE WHEN status = 'Used' THEN 1 ELSE 0 END) as used_units,
                SUM(CASE WHEN status = 'Expired' THEN 1 ELSE 0 END) as expired_units
            ")
            ->groupBy('blood_type')
            ->orderBy('blood_type')
            ->get();

        $bags = BloodInventory::latest()->get();

        return view('inventory.index', compact('summary', 'bags'));
    }

    public function markUsed($id)
    {
        $bag = BloodInventory::findOrFail($id);

        if ($bag->status !== 'Available') {
            return back()->with('error', 'Only available blood bags can be marked as used.');
        }

        $bag->update([
            'status' => 'Used',
        ]);

        return back()->with('success', 'Blood bag marked as used.');
    }
    public function publicStock()
    {
        BloodInventory::where('status', 'Available')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->update(['status' => 'Expired']);

        $summary = BloodInventory::selectRaw("
                blood_type,
                SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_units
            ")
            ->groupBy('blood_type')
            ->orderBy('blood_type')
            ->get();

        return view('inventory.public-stock', compact('summary'));
    }
}