<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\BloodInventoryController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// 1. Public Routes
// ─────────────────────────────────────────────

Route::get('/', fn() => view('welcome'));

// ─────────────────────────────────────────────
// 2. Authentication Routes (Breeze)
// ─────────────────────────────────────────────

require __DIR__ . '/auth.php';

// ─────────────────────────────────────────────
// 3. OTP Verification (auth only)
// ─────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/verify-otp',  [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify.post');

    Route::get('/blood-requests', [BloodRequestController::class, 'index'])->name('blood.requests');
});

// ─────────────────────────────────────────────
// 4. Protected Routes (auth + OTP verified)
// ─────────────────────────────────────────────

Route::middleware(['auth', 'otp'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Hospital
    Route::get('/hospital', [HospitalController::class, 'index'])->name('hospital.index');
    Route::get('/hospital/coverage', [BloodRequestController::class, 'donorCoverage'])->name('hospital.coverage');

    // Donations
    Route::get('/donations/create', [HospitalController::class, 'create'])->name('donations.create');
    Route::post('/donations',       [HospitalController::class, 'store'])->name('donations.store');

    Route::post('/donation/{id}/checkin',     [HospitalController::class, 'checkIn'])->name('checkin.donation');
    Route::post('/donation/{id}/verify',      [HospitalController::class, 'verify'])->name('verify.donation');
    Route::get('/donation/{id}/certificate',  [HospitalController::class, 'downloadCertificate'])->name('download.certificate');

    // Blood Requests
    Route::get('/blood-requests/create',        [BloodRequestController::class, 'create'])->name('blood.requests.create');
    Route::post('/blood-requests',              [BloodRequestController::class, 'store'])->name('blood.requests.store');
    Route::post('/blood-requests/{id}/complete',[BloodRequestController::class, 'complete'])->name('blood.requests.complete');
    Route::get('/blood-requests/{id}/nearby',   [BloodRequestController::class, 'findNearbyDonors'])->name('blood.requests.nearby');
});

// ─────────────────────────────────────────────
// 5. Blood Inventory (unprotected — review if intentional)
// ─────────────────────────────────────────────

Route::get('/blood-inventory',          [BloodInventoryController::class, 'index'])->name('inventory.index');
Route::post('/blood-inventory/{id}/used',[BloodInventoryController::class, 'markUsed'])->name('inventory.used');



Route::get('/blood-stock', [BloodInventoryController::class, 'publicStock'])
    ->name('blood.stock.public');

Route::middleware(['auth'])->group(function () {
    Route::get('/blood-inventory', [BloodInventoryController::class, 'index'])
        ->name('inventory.index');

    Route::post('/blood-inventory/{id}/used', [BloodInventoryController::class, 'markUsed'])
        ->name('inventory.used');
});