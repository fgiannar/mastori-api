<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;

use Auth;
use App\Http\Services\NexmoSmsService;
use App\VerificationCode;

class UsersController extends Controller
{
  public function sendVerificationCode() {
    $user = Auth::user();
    $vObj = VerificationCode::generate($user->id);
    $phone = str_replace(['(', ')'], '', $user->userable->phone);  
    $smsService = new NexmoSmsService();
    $sent = $smsService->send( $phone, 'mastori', 'Ο κωδικός επαλήθευσης είναι: ' . $vObj->code);
    if($sent) {
      return response()->json(['status' => 'ok'], 200);
    } else {
      return response()->json(['error' => 'Sms error'], 500);
    }
  }

  public function verifyCode(Request $request) {
      $filterColumns = [
          'code'   => 'code',
      ];
      $now = date('Y-m-d H:i:s', time());
      $codes = VerificationCode::filterColumns($filterColumns);
      $user = Auth::user();
      //set verifiaction time to user
      $user->mobile_verified = $now;
      $user->save();
      $codes->where('user_id', '=', $user->id)->where('active', '=', 1)->where('expiration', '>=', $now);
      if (!count($codes->get())) {
        return response()->json(['error' => 'code not found'], 404);
      } else {
        $code = $codes->get()[0];
        $code->active = 0; //diactivate used code
        $code->save();
        return response()->json(['status' => 'ok'], 200);
      }
  }

}
