<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    return $next($request); // <--- Add this line here temporarily!

    // The rest of your code...
    if (!session()->has('otp_verified')) {
        return redirect()->route('otp.verify');
    }
}
}
