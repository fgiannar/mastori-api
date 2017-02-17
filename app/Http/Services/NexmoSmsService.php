<?php

namespace App\Http\Services;
use Config;
use App\SmsLog;
use Log;
use Nexmo\Laravel\Facade\Nexmo;


/**
*@to do add sms logging system
*/
class NexmoSmsService
{

  private $nexmoMessage;

  public function __construct() {

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

      try {
        $message =   Nexmo::message()->send([
              'type' => 'unicode',
              'to' => $receiver,
              'from' => $sender,
              'text' => $text
            ]);
          } catch(\Exception $e) {
              $message = false;
        };

      //add sms log record
      $smsLog = new SmsLog();
      $smsLog->receiver = $receiver;
      $msgStatus = $message->getStatus();
      if ($message) {
        $msgStatus = $message->getStatus();
        $smsLog->status = $msgStatus;
        //successfully sent
        if ($msgStatus == 0) {
          $smsLog->messageid = $message->getMessageId();
        } //error - msg has not been sent
         else {
          $smsLog->errortext = $message->getDeliveryError() . $message->getDeliveryLabel();
        }
      } else {
        //unexpected response format
        $smsLog->status = 'FAILED';
      }
      $smsLog->save();
      return $msgStatus == 0;
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
