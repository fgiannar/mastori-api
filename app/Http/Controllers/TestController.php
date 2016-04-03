<?php
//used for testing purposes
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Services\NexmoSmsService;

use Config;

class TestController extends Controller
{

    public function test()
    {
    //  $nexmo_sms = new \NexmoMessage('api_key', 'api_secret');
      $smsService = new NexmoSmsService();
      $sent = $smsService->send( '+306937078135', 'tania', 'σκατά ελληνικά και ψδωμά@δσδ' );
      //$receive = $smsService->receive();
      dd($sent);
    }



}
