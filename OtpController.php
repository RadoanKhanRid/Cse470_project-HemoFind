<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function show()
    {
        if (!session()->has('auth.id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if (!session()->has('auth.id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('auth.id'));

        if ($user->otp_code !== $request->otp || $user->otp_expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => 'The provided OTP is invalid or has expired.',
            ]);
        }

        // Clear OTP
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Log the user in
        Auth::login($user, session('auth.remember', false));

        // Clear session data
        session()->forget(['auth.id', 'auth.remember']);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
