<?php

namespace App\Http\Services;
use Config;
use Log;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;



class SparkPostEmailService
{

  private $sparky;

  public function __construct() {
    $httpClient = new GuzzleAdapter(new Client());
    $this->sparky = new SparkPost($httpClient, ['key'=>Config::get('services.sparkpost.api_key')]);
  }


  /**
   * Sends the request to sparkhost api
   * @param array payload
   * @return boolean status
   */
  private function send($mailPayload) {
    $promise = $this->sparky->transmissions->post($mailPayload);
    try {
      $response = $promise->wait();
      if ($response->getStatusCode() == 200) {
        return true;
      } else {
        return false;
      }
    } catch (\Exception $e) {
      Log::error('Email sent failed with code: '.$e->getCode(). ' and message : '. $e->getMessage());
      return false;
    }

  }
  /**
   * Sends an html email to recipient using Sparkhost
   * @param  mixed $recipients, the $recipient(s)
   * @param string $html, the html content if it's not template
   * @param string $subject, the mail subject
   * @param string $fromAddress, the sender's email - if not provided it uses the generic from config
   * @param string $fromName, the sender's name - if not provided it uses the generic from config
   * @return boolean (sent status)
   */
  public function sendHtml($recipients, $html, $subject, $fromAddress = null, $fromName = null) {
     $recipients = $this->makeRecipients($recipients);
     $fromName = $fromName ?: Config::get('mastori.contact.email_name');
     $fromAddress = $fromAddress ?: Config::get('mastori.contact.email_address');
     $from = ['name' => $fromName, 'email' => $fromAddress];
     $content =  ['html' => $html, 'from'=>$from , 'subject' => $subject];
     $payLoad = [
      'content' => $content,
      'recipients' => $recipients
      ];
    return $this->send($payLoad);
  }

  /**
   * Sends an email to recipient using Sparkhost and template
   * @param  mixed $recipients, the $recipients
   * @param string $template_id, the template_id to use
   * @param arrat $substitution_data the $substitution_data for the template
   * @return boolean (sent status)
   */
  public function sendTemplate($recipients, $template_id,  $substitution_data) {
    $content = ['template_id' => $template_id];
    $recipients = $this->makeRecipients($recipients);
    $payLoad = [
      'content' => $content,
      'substitution_data' => $substitution_data,
      'recipients' => $recipients
    ];
    return $this->send($payLoad);
  }


  /**
   * Formats the recepient data to be sent
   * @param mixed $recipients (single email, or array of emails)
   * @return array to be sent to mail service
   */
  private function makeRecipients($recipients) {
    $recipients = is_array($recipients) ? $recipients : [$recipients];
    $recipientsArray= [];
    foreach ($recipients as $recepient) {
      $recipientsArray[] = ['address' => ['email' => $recepient]];
    }
    return $recipientsArray;
  }



}
