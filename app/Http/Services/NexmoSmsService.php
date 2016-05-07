<?php

namespace App\Http\Services;
use Config;
use App\SmsLog;

/**
*@to do add sms logging system
*/
class NexmoSmsService
{

  private $nexmoMessage;

  public function __construct() {
    $this->nexmoMessage =  new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
  }

  /**
   * Uses Nexmo API to send sms to single receiver
   * Adds a log record to SmsLog table
   *
   * @param  string $receiver, the receiver
   * @param string $sender, the from name
   * @param string text, the message
   * @return boolean (sent status)
   */
  public function send($receiver, $sender, $text) {
      $smsService = new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
      $sent =  $this->nexmoMessage->sendText( $receiver, $sender, $text );
      //add sms log record
      $smsLog = new SmsLog();
      $smsLog->receiver = $receiver;
      if (isset($sent->messages) && is_array($sent->messages)) {
        $msg = $sent->messages[0];
        $smsLog->status = $msg->status;
        //successfully sent
        if ($msg->status == 0) {
          $smsLog->messageid = $msg->messageid;
        } //error - msg has not been sent
         else {
          $smsLog->errortext = $msg->errortext;
        }
      } else {
        //unexpected response format
        $smsLog->status = 'FAILED';
      }
      $smsLog->save();
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
