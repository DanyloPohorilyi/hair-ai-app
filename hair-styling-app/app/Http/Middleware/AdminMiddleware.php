<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('customer')->check() && Auth::guard('customer')->user()->is_admin) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'У вас немає доступу.');
    }
}
