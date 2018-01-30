<?php

namespace App\Http\Services;
use Config;
use Log;
use App\Http\Services\SparkPostEmailService;

/**
 * Email class to send transactional emails, using templates on sparkhost
 * Use sparkhost as a MAIL_DRIVER and laravel's Mail function to send html, or text emails
 *
 * Usage: $e = new Email('tania.pets@gmail.com', 'test-template', ['name' => 'Φιφή']);  $e->send();
 */
class Email
{
    protected $receiver;
    protected $template;
    protected $data;

    public function __construct($receiver, $template = null, $data = null) {
        $this->receiver = $receiver;
        $this->template = $template;
        $this->data = $data;
    }

  /**
   */
  public function send() {
      $mService = new SparkPostEmailService();
      return $mService->sendTemplate($this->receiver, $this->template, $this->data);
  }


}
