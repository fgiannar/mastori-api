<?php

namespace App\Http\Services;
use Config;

/**
*@to do add sms logging system
*/
class NexmoSmsService
{

  private $nexmoMessage;

  public function __construct() {
    $this->nexmoMessage =  new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
  }

  public function send($receiver, $sender, $text) {
      $smsService = new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
      $sent =  $this->nexmoMessage->sendText( $receiver, $sender, $text );
      return $sent->messages[0]->status == 0;
  }

  //@todo need to rent a number
  // public function receive() {
  //     if($this->nexmoMessage->inboundText()) {
  //       return $this->nexmoMessage->text;
  //     } else {
  //       return false;
  //     }
  // }
}
