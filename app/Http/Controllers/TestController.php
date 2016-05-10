<?php
//used for testing purposes
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Services\NexmoSmsService;

use App\EndUser;

use Config;

use Mail;

class TestController extends Controller
{

    public function test()
    {
      $data = ['test' => 'test'];
      Mail::send('emails.demo', $data, function($message)
      {
          
          $message->to('tania.pets@gmail.com', 'Jane Doe')->subject('This is a demo!');
      });
    //  $nexmo_sms = new \NexmoMessage('api_key', 'api_secret');
      // $smsService = new NexmoSmsService();
      // $sent = $smsService->send( '+306937078135', 'tania', 'σκατά ελληνικά και ψδωμά@δσδ' );
      // //$receive = $smsService->receive();
      // dd($sent);
    }

    public function testpoints()
    {
    	$endUser = EndUser::first();
			$amount = 10; // (Double) Can be a negative value
			$message = "The reason for this transaction";

			//Optional (if you modify the point_transaction table)
			$data = [
			    // 'ref_id' => 'someReferId',
			];

			$transaction = $endUser->addPoints($amount,$message,$data);

			return $endUser->load('user');
    }



}
