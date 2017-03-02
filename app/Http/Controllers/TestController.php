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

class TestController extends Controller
{

    public function test()
    {

      $mService = new SparkPostEmailService();

        // $mService->send('tania.pets@gmail.com', null, 'test-template', ['name'=>'Σουλτάνα', 'msg' => 'Είσαι βλάκας']);


        $mService->send('tania.pets@gmail.com', '<b>hii<br/>ouou', null, ['name'=>'Σουλτάνα', 'msg' => 'Είσαι βλάκας']);


//       $httpClient = new GuzzleAdapter(new Client());
// $sparky = new SparkPost($httpClient, ['key'=>Config::get('services.sparkpost.api_key')]);
//
// $promise = $sparky->transmissions->post([
//     'content' => ['template_id' => 'test-template'],
//     // 'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
//     'recipients' => [
//         [
//             'address' => [
//                 'name' => 'tania',
//                 'email' => 'tania.pets@gmail.com',
//             ],
//         ],
//     ],
// ]);
// try {
//     $response = $promise->wait();
//     echo $response->getStatusCode()."\n";
//     print_r($response->getBody())."\n";
// } catch (\Exception $e) {
//     echo $e->getCode()."\n";
//     echo $e->getMessage()."\n";
// }
//
// exit;
//       $data = ['test' => 'test'];
//       Mail::send('emails.demo', $data, function($message)
//       {
//
//           $message->from('postmaster@mastori.gr')->to('tania.pets@gmail.com', 'Jane Doe')->subject('This is a demo!');
//       });
//     //  $nexmo_sms = new \NexmoMessage('api_key', 'api_secret');
//       // $smsService = new NexmoSmsService();
//       // $sent = $smsService->send( '+306937078135', 'tania', 'σκατά ελληνικά και ψδωμά@δσδ' );
//       // //$receive = $smsService->receive();
//       // dd($sent);
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
