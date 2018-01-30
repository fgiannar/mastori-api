<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Http\Services\Email;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Log;

class SendEmailVerification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  App\Events\UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $user = $event->user;
        $mail = new Email($user->email, 'email-confirmation', ['user_name' => $user->userable->name, 'verification_link' => url('auth/confirm-email/' . $user->mail_token) ]);
        $mail->send();
    }
}
