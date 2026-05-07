<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // Active donations as donor (Outgoing Help)
        $activeDonation = \App\Models\Donation::where('donor_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['bloodRequest', 'donor'])
            ->latest()
            ->first();

        // Active donations as requester (Incoming Help)
        $incomingHelp = \App\Models\Donation::whereHas('bloodRequest', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['bloodRequest', 'donor'])
            ->latest()
            ->first();

        return view('dashboard', compact('activeDonation', 'incomingHelp'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Protected Search Routes
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::post('/search', [SearchController::class, 'search'])->name('search.process');

    // Blood Requests Dashboard
    Route::get('/requests', [\App\Http\Controllers\BloodRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [\App\Http\Controllers\BloodRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [\App\Http\Controllers\BloodRequestController::class, 'store'])->name('requests.store');
    Route::delete('/requests/{bloodRequest}', [\App\Http\Controllers\BloodRequestController::class, 'destroy'])->name('requests.destroy');

    // Donor Profile / Check-in
    Route::get('/checkin', [\App\Http\Controllers\DonorProfileController::class, 'show'])->name('donor.profile.show');
    Route::patch('/checkin', [\App\Http\Controllers\DonorProfileController::class, 'update'])->name('donor.profile.update');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return "Admin Dashboard";
        })->name('admin.dashboard');
    });

    // Donation Tracking & Chat
    Route::post('/requests/{bloodRequest}/accept', [\App\Http\Controllers\DonationController::class, 'accept'])->name('donation.accept');
    Route::post('/donations/{donation}/cancel', [\App\Http\Controllers\DonationController::class, 'cancel'])->name('donation.cancel');
    Route::get('/donations/{donation}/track', [\App\Http\Controllers\DonationController::class, 'track'])->name('donation.track');
    Route::get('/donations/{donation}/data', [\App\Http\Controllers\DonationController::class, 'getData'])->name('donation.data');
    Route::post('/donations/{donation}/location', [\App\Http\Controllers\DonationController::class, 'updateLocation'])->name('donation.location.update');
});

require __DIR__.'/auth.php';

