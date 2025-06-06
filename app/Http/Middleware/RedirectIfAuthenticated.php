<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
          case 'admin':
            if (Auth::guard($guard)->check()) {
                $admin = Auth::guard('admin')->user();
                if ($admin->email === 'faizankhan2595@gmail.com') {
                    return redirect()->route('user.index');
                }
                return redirect()->route('admin.dashboard');
            }
            break;
    
          default:
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
            break;
        }
    
        return $next($request);
    }
}
