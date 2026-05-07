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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $user = \App\Models\User::where($field, $login)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($request->throttleKey());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($request->throttleKey());

        // Generate and send OTP
        $user->generateOtp();

        // Store user ID in session temporarily
        session([
            'auth.id' => $user->id,
            'auth.remember' => $request->boolean('remember'),
        ]);

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
