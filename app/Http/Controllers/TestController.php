<?php
//used for testing purposes
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Config;

class TestController extends Controller
{

    public function test()
    {
    //  $nexmo_sms = new \NexmoMessage('api_key', 'api_secret');
      $sms = new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
      $s = $sms->sendText( '+306937078135', 'MyApp', 'ελληνικά και ψδωμά@δσδ' );
      dd($s);
    }



}
