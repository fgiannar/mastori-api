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
   * Sends an email to recipient using Sparkhost
   * as a  content it can accept html, or template_id
   * @param  string $receiver, the receiver
   * @param string $html, the html content if it's not template
   * @param string $template_id, the template_id to use
   * @todo add subject, replyto etc, when it's html and not template
   * @return boolean (sent status)
   */
  public function send($receiver, $html = null, $template_id = null, $substitution_data = []) {
     $content = $html ? ['html' => $html, 'from'=>'postmaster@mastori.gr'] : ['template_id' => $template_id];
     $promise = $this->sparky->transmissions->post([
      'content' => $content,
      'substitution_data' => $substitution_data,
      'recipients' => [
          [
              'address' => [
                  'email' => $receiver
              ],
          ],
      ],
    ]);
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



}
