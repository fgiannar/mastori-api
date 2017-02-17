<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\Controller;
use Config;
use Auth;
use App\EndUser;
use App\User;

class AuthenticateController extends Controller
{
    private $claims;

    /**
    *     @SWG\Definition(
    *         definition="credentialsModel",
    *         required={"email, username, password"},
    *         @SWG\Property(
    *             property="email",
    *             type="string"
    *         ),
    *         @SWG\Property(
    *             property="username",
    *             type="string"
    *         ),
    *         @SWG\Property(
    *             property="password",
    *             type="string"
    *         )
    *     )
    */

    /**
    *     @SWG\Definition(
    *         definition="tokenModel",
    *         required={"token"},
    *         @SWG\Property(
    *             property="token",
    *             type="string"
    *         )
    *     )
    */


    /**
    *     @SWG\Definition(
    *         definition="providerModel",
    *         required={"provider"},
    *         @SWG\Property(
    *             property="provider",
    *             type="string"
    *         )
    *     )
    */

    /**
    *     @SWG\Definition(
    *         definition="fbObj",
    *         required={"code, clientId, redirectUri"},
    *         @SWG\Property(
    *             property="code",
    *             type="string"
    *         ),
    *         @SWG\Property(
    *             property="clientId",
    *             type="string"
    *         ),
    *         @SWG\Property(
    *             property="redirectUri",
    *             type="string"
    *         )
    *     )
    */


    // TODO Change this for production (claims should NOT expire in a week)
    public function __construct()
    {
        $this->claims = array('exp' => time() + (7 * 24 * 60 * 60));
    }



    /**
     * @SWG\Post(
     *     path="/auth",
     *     operationId="authenticate",
     *     tags={"auth"},
     *     description="Authenticates a user",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/credentialsModel")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns access token",
     *         @SWG\Schema(ref="#/definitions/tokenModel")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="invalid credentials",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     ),
     * )
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $emailOrUsername = $request->input('email') ? 'email' : 'username';
        $credentials = $request->only($emailOrUsername, 'password');

        $claims = $this->claims;

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials, $claims)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user = Auth::user()->userable->load('user');;
        $user->type = Auth::user()->userable_type == 'App\EndUser' ? 'enduser' : 'mastori';
        $user->mobile_verified = Auth::user()->mobile_verified;
        // all good so return the token
        return response()->json(compact('token', 'user'));
    }


    /**
     * @SWG\Post(
     *     path="/auth/facebook",
     *     operationId="fbAuthenticate",
     *     tags={"auth"},
     *     description="Authenticates a user via facebook",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/fbObj")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns access token",
     *         @SWG\Schema(ref="#/definitions/tokenModel")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="could_not_create_token",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     ),
     * )
     */
    /**
     * Login with Facebook.
     */
    public function facebook(Request $request)
    {
        $accessTokenUrl = 'https://graph.facebook.com/v2.3/oauth/access_token';
        $graphApiUrl = 'https://graph.facebook.com/v2.3/me';
        $params = [
            'code' => $request->input('code'),
            'client_id' => $request->input('clientId'),
            'redirect_uri' => $request->input('redirectUri'),
            'client_secret' => Config::get('services.facebook.app_secret')
        ];
        $client = new GuzzleHttp\Client();

        try {
            // Step 1. Exchange authorization code for access token.
            $accessTokenRes = $client->get($accessTokenUrl, ['query' => $params]);
            $accessTokenArr = json_decode($accessTokenRes->getBody(), true);

            $params = [
                'fields' => 'email,name,id',
                'access_token' => $accessTokenArr['access_token']
            ];
            // Step 2. Retrieve profile information about the current user.
            $profileRes = $client->get($graphApiUrl, ['query' => $params]);
            $profile = json_decode($profileRes->getBody(), true);
        } catch (ClientException $e) {

            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // Step 3a. If user is already signed in then link accounts.
        if ($request->header('Authorization'))
        {
            $user = User::where('facebook_id', '=', $profile['id']);
            if ($user->first())
            {
                return response()->json(['error' => 'There is already a Facebook account that belongs to you'], 409);
            }

            $user = JWTAuth::parseToken()->authenticate();

            $user->facebook_id = $profile['id'];

            $user->update();

            return $user->userable;
        }
        // Step 3b. Login user
        else
        {
            $claims = $this->claims;

            $user = User::where('facebook_id', '=', $profile['id']);
            if ($user->first())
            {
                try {
                    // attempt to verify the credentials and create a token for the user
                    if (! $token = JWTAuth::fromUser($user->first(), $claims)) {
                        return response()->json(['error' => 'invalid_credentials'], 401);
                    }
                } catch (JWTException $e) {
                    // something went wrong whilst attempting to encode the token
                    return response()->json(['error' => 'could_not_create_token'], 500);
                }

                return response()->json(compact('token'));
            }

            // need to register user (return profile 422 to indicate registration required data missing)
            return response(array_except($profile, 'id'), 422);
        }
    }



    /**
     * @SWG\Post(
     *     path="/auth/unlink",
     *     @SWG\Parameter(
     *         description="Access token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string"
     *     ),
     *     operationId="unlinkfb",
     *     tags={"auth"},
     *     description="Unlinks a facebook account",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/providerModel")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Ok",
     *         @SWG\Schema(ref="#/definitions/tokenModel")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="user not found",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
    /**
     * Unlink Facebook.
     */
    public function unlink(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user)
        {
            return response()->json(['error' => 'User not found'], 404);
        }

        switch ($request->input('provider')) {
            case 'facebook':
                $user->facebook_id = '';
                break;

            default:
                # code...
                break;
        }


        $user->update();

        return 'OK';
    }

    /**
     * Check if there is an existing resource with the provided email.
     * @param string email
     */
    private static function checkEmailExists($email) {

        $user = User::where('email', '=', $email);

        return $user->first();
    }
}
