<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;


class AuthenticateMastoriController extends AuthenticateController
{

    public function index(Request $request)
    {
        config(['auth.defaults.guard' => 'api-mastoria']);
        // grab credentials from the request
        $credentials = $request->only('username', 'password');

        return parent::authenticate($credentials);
    }
}
