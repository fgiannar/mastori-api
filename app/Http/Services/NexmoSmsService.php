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
    //init Nexmo Client
    $this->nexmoMessage =  new \NexmoMessage(Config::get('services.nexmo.api_key'), Config::get('services.nexmo.api_secret'));
  }

  /**
   * Uses Nexmo API to send sms to single receiver
   * Adds a log record to SmsLog table
   * @todo think about other country codes
   * @param  string $receiver, the receiver
   * @param string $sender, the from name
   * @param string text, the message
   * @return boolean (sent status)
   */
  public function send($receiver, $sender, $text) {
      //format the phone number
      $receiver = $this->formatNumber($receiver, 'GR');
      //send the message
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


  /**
   * Formats phone number to +306937078135 format
   *
   * @param string $phone, the phone number
   * @return    string, the formatted phone
   */
  private function formatNumber($phone, $country) {
    $countryCode = $this->getCountryPrefix($country);
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);
    switch($length) {
      case 10:
        $phone = '+' .  $countryCode . $phone;
        break;
      case 12:
        $phone = '+' . $phone;
        break;
    }
    return $phone;
  }

  /**
   * Returns phone country prefix for given country
   *
   * @param string, ISO country code
   * @return    string, phone country prefix
   */
  private function getCountryPrefix($country) {
    switch($country) {
      case 'GR':
        return '30';
        break;
    }
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
