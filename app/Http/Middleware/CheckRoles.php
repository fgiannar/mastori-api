<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Auth;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
    	$rolesArr = explode('|', strtolower($roles));

    	if (empty($rolesArr)) {
    		throw new \Exception("Must provide at least one role", 1);

    	}

    	$currentRole = str_replace('app\\', '', strtolower(Auth::user()->userable_type));

        if (in_array($currentRole, $rolesArr)) {
            return $next($request);
        }

        return response('Unauthorized. Allowed roles: ' . $roles . '. Current role: ' . $currentRole, 401);
    }
}
