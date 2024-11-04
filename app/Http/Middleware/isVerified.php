<?php

namespace App\Http\Middleware;

use Closure;

class isVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => "Please verify you email."
            ],401);
        }
        return $next($request);
    }
}
