<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

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
    public function handle($request, Closure $next)
    {
        return $next($request);
        // if (!JWTAuth::getToken()) {
        //     return $next($request);
        // }
        //
        //
        // return response('Uanuthorized. Allowed only not authenticated users.', 401);
    }
}
