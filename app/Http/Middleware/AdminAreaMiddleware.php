<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 01.10.25
 * Time: 12:59.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAreaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && $request->user()->isAdmin()) {
            return $next($request);
        }

        return to_route('home')->with('error', 'You do not have access to the admin area.');
    }
}
