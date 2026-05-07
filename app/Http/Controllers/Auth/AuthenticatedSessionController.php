<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request)
{
    // 1. Validate the email and password FIRST
    $request->authenticate();

    // 2. Now that the user is logged in, Laravel knows who they are
    $otp = rand(100000, 999999);
    
    $request->user()->update([
        'otp' => $otp,
        'otp_expires_at' => now()->addMinutes(10),
    ]);

    // 3. Log it so you can see it
    \Log::info("Your Login OTP is: " . $otp);

    // 4. Create the session and redirect
    $request->session()->regenerate();

    return redirect()->route('otp.verify');
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
