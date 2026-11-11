<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your sign-in session expired before the admin page opened. Please log in again.',
            ]);
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('login')->withErrors([
                'email' => 'This account does not have admin access.',
            ]);
        }

        return $next($request);
    }
}
