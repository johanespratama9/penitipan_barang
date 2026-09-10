<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect kasir-only users to /pos when they try to visit /admin pages.
 * Admin & super_admin can access both /admin and /pos without restriction.
 */
class RedirectKasirToPos
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user !== null
            && $user->hasRole('kasir')
            && ! $user->hasAnyRole(['admin', 'super_admin'])
            // Allow /admin/login and /admin/logout so kasir can still authenticate
            && ! $request->is('admin/login', 'admin/logout', 'admin/login/*')
        ) {
            return redirect('/pos');
        }

        return $next($request);
    }
}

