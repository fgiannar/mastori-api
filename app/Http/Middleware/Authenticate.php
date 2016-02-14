<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Config;

class Authenticate extends GetUserFromToken
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
        // if (Auth::guard($guard)->guest()) {
        //     if ($request->ajax() || $request->wantsJson()) {
        //         return response('Unauthorized.', 401);
        //     } else {
        //         return redirect()->guest('login');
        //     }
        // }

        // config(['jwt.user' => 'App\User']);
        config(['auth.defaults.guard' => $guard]);

        return parent::handle($request, $next);
    }
}