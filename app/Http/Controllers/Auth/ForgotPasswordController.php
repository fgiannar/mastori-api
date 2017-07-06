<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Validator;
use App\Notifications\PasswordReset;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails.
    | Since this is an API and I don't wont to create a view or compicate stuff
    | too much, we just create a random pass and send it to the user
    |
     */

    // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 401);
        }

        // Generate random password
        $randomPass = str_random(8);
        $password = bcrypt($randomPass);

        // save user password
        $user = User::where('email', $request->email)->first();
        $user->password = $password;
        $user->save();

        // notify user
        $user->notify(new PasswordReset($randomPass));

        return response()->json(['status' => 'ok']);
    }
}
