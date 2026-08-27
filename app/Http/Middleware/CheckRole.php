<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {   
        // use for check user login already or not
        if(!$request -> user()) {
            return response()->json([
                'message' => 'Unauthorized',
            ],401);
        }

        $userRole = $request->user()->role_id;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Forbidden. You do not have permission to access this resource.'
            ], 403);
        }


        return $next($request);
    }
}
