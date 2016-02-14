<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;


class AuthenticateUserController extends AuthenticateController
{

    public function index(Request $request)
    {
        // config(['auth.defaults.guard' => 'api-users']);
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        return parent::authenticate($credentials);
    }
}
