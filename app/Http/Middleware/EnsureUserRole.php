<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     * Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])
     *     ->middleware('role:doctor');
     *
     * Or multiple roles:
     * Route::get('/admin', [AdminController::class, 'dashboard'])
     *     ->middleware('role:admin,doctor');
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You do not have permission to access this resource',
        ], 403);
    }
}
