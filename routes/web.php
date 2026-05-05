<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BloodRequestController;
use Illuminate\Support\Facades\Route;

// 1. Public Routes
Route::get('/', function () {
    return view('welcome');
});

// 2. Authentication Routes (Login, Register, etc. from Breeze)
require __DIR__.'/auth.php';

// 3. OTP Verification Routes (Only requires basic Login)
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify.post');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/blood-requests', [BloodRequestController::class, 'index'])->name('blood.requests');
});



// 4. Protected Routes (Requires Login AND OTP Verification)
Route::middleware(['auth', 'otp'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () { 
        return view('dashboard'); 
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Hospital & Donations
    Route::get('/hospital', [HospitalController::class, 'index'])->name('hospital.index');
    Route::post('/donation/{id}/checkin', [HospitalController::class, 'checkIn'])->name('checkin.donation');
    Route::post('/donation/{id}/verify', [HospitalController::class, 'verify'])->name('verify.donation');
    Route::get('/donation/{id}/certificate', [HospitalController::class, 'downloadCertificate'])->name('download.certificate');
    Route::get('/blood-requests/{id}/nearby', [BloodRequestController::class, 'findNearbyDonors'])->name('blood.requests.nearby');
    // This shows the form
Route::get('/blood-requests/create', [BloodRequestController::class, 'create'])->name('blood.requests.create');

// This saves the form data (Step 6 from before)
Route::post('/blood-requests', [BloodRequestController::class, 'store'])->name('blood.requests.store');
Route::get('/donations/create', [HospitalController::class, 'create'])->name('donations.create');
Route::post('/donations', [HospitalController::class, 'store'])->name('donations.store');
Route::post('/blood-requests/{id}/complete', [BloodRequestController::class, 'complete'])
    ->name('blood.requests.complete');
// Analytics Route
Route::get('/hospital/coverage', [BloodRequestController::class, 'donorCoverage'])
    ->name('hospital.coverage');
// The action that SAVES the data
Route::post('/donations/store', [HospitalController::class, 'store'])->name('donations.store');
});