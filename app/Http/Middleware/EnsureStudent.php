<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'student') {
            abort(403, 'Access denied. This area is restricted to students only.');
        }

        // Check if the student account has been deactivated by admin
        if (property_exists(auth()->user(), 'is_active') || array_key_exists('is_active', auth()->user()->getAttributes())) {
            if (!auth()->user()->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
            }
        }

        return $next($request);
    }
}

