<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'baseurl' => 'https://graph.facebook.com/',
        'tokenurl' => '/oauth/access_token',
        'version' => 'v2.5',
        'app_id' =>  env('FACEBOOK_ID'),
        'app_secret' =>  env('FACEBOOK_SECRET'),
        'v' => 20130815,
    ],
    'points' => [
        'online_appointment' => 1000,
        'review' => 1000
    ],
    'sparkpost' => [
      'secret' => '1d0a7964bfb64d64acefe5ced029b4ed5c924a23'
    ]

];
