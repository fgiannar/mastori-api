<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;

class AuthenticateController extends Controller
{

    public function authenticate($credentials)
    {

        $claims = array('exp' => time() + (7 * 24 * 60 * 60));
$token = Auth::guard('api-users')->attempt($credentials, $claims);
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = Auth::guard('api-users')->attempt($credentials, $claims)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (\Exception $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
}
