<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsManajer
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->user()->role, ['owner', 'manajer'])) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya owner atau manajer yang bisa mengakses ini.'
            ], 403);
        }

        return $next($request);
    }
}