<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;

class AuthenticateController extends Controller
{

    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $emailOrUsername = $request->input('email') ? 'email' : 'username';
        $credentials = $request->only($emailOrUsername, 'password');

        $claims = array('exp' => time() + (7 * 24 * 60 * 60));

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials, $claims)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
}
