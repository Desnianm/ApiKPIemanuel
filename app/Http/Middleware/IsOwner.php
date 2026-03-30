<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsOwner
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'owner') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya owner yang bisa mengakses ini.'
            ], 403);
        }

        return $next($request);
    }
}