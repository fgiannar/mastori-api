<?php

namespace App\Http\Services;
use Config;


class NexmoSmsService
{

  private $nexmoMessage;

  public function __construct() {
    $this->nexmoMessage =  new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
  }

  public function send($receiver, $sender, $text) {
      $smsService = new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
      return $this->nexmoMessage->sendText( $receiver, $sender, $text );
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
