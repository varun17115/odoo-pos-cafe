<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Redirect to their appropriate home
            return match(auth()->user()->role) {
                'chef'    => redirect()->route('kitchen.display')->with('error', 'Access denied.'),
                'cashier' => redirect()->route('pos.terminal')->with('error', 'Access denied.'),
                default   => abort(403),
            };
        }

        return $next($request);
    }
}
