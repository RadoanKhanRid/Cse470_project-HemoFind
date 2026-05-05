<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller {
    public function showVerifyForm() {
        return view('auth.verify-email');
    }

    public function verify(Request $request) {
        $request->validate(['otp' => 'required|numeric|digits:6']);
        $user = Auth::user();

        if ($user->otp == $request->otp && now()->lt($user->otp_expires_at)) {
            $user->update(['otp' => null, 'otp_expires_at' => null]);
            session(['otp_verified' => true]);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['otp' => 'The provided OTP is invalid or expired.']);
    }
}