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

        if ($user !== null && ! $user->hasAnyRole(['admin', 'super_admin'])) {
            // 1. Role Kasir diarahkan ke /pos (kecuali rute auth & dapur)
            if (
                $user->hasRole('kasir')
                && ! $request->is('dasbor/login', 'dasbor/logout', 'dasbor/login/*', 'dasbor/dapur', 'dasbor/dapur/*')
            ) {
                return redirect('/pos');
            }

            // 2. Role Dapur dikunci hanya ke Layar Dapur (/dapur atau /dasbor/dapur)
            if (
                $user->hasRole('dapur')
                && ! $request->is('dasbor/login', 'dasbor/logout', 'dasbor/login/*', 'dasbor/dapur', 'dasbor/dapur/*')
            ) {
                return redirect()->route('kitchen.display');
            }
        }

        return $next($request);
    }
}

