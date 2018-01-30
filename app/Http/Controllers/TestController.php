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

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;


use App\Http\Services\SparkPostEmailService;

use App\Http\Services\Email;

class TestController extends Controller
{

    public function test()
    {

        $e = new Email('tania.pets@gmail.com', 'test-template', ['name' => 'Φιφή']);
        $f = $e->send();
        dd($f);

        exit;

        //Email
        Mail::send('emails.demo', [], function ($message) {
            $message
              ->from('info@mastori.gr', 'Your Name')
              ->to('tania.pets@gmail.com', 'Receiver Name')
              ->subject('From SparkPost with ❤');
          });

        exit;

      $mService = new SparkPostEmailService();


        $html = '<b>hii<br/>ouou';
        $subject = 'Ωραίο μήνυμα';
        $fromName = 'Σουλτάνα';

        $fromAddress = 'tania@mastori.gr';

        // $sent = $mService->sendHtml('tania.pets@gmail.com', $html ,$subject, $fromAddress, $fromName);
        //
        //
        // $sent = $mService->sendHtml('tania.pets@gmail.com', $html ,$subject);
        //

        $sent = $mService->sendTemplate('tania.pets@gmail.com', 'email-confirmation', ['name'=>'Σουλτάνα']);

        dd($sent);

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
