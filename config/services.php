<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],
    'prostor_sms' => [
        'base_url' => env('PROSTOR_BASE_URL'),
        'login_2' => env('PROSTOR_SMS_LOGIN_2'),
        'password_2' => env('PROSTOR_SMS_PASSWORD_2'),
        'login_4' => env('PROSTOR_SMS_LOGIN_4'),
        'password_4' => env('PROSTOR_SMS_PASSWORD_4'),
        'login_5' => env('PROSTOR_SMS_LOGIN_5'),
        'password_5' => env('PROSTOR_SMS_PASSWORD_5'),
        'login_7' => env('PROSTOR_SMS_LOGIN_7'),
        'password_7' => env('PROSTOR_SMS_PASSWORD_7'),
        'login_10' => env('PROSTOR_SMS_LOGIN_10'),
        'password_10' => env('PROSTOR_SMS_PASSWORD_10'),
        'login_12' => env('PROSTOR_SMS_LOGIN_12'),
        'password_12' => env('PROSTOR_SMS_PASSWORD_12'),
        'login_15' => env('PROSTOR_SMS_LOGIN_15'),
        'password_15' => env('PROSTOR_SMS_PASSWORD_15'),
        'login_17' => env('PROSTOR_SMS_LOGIN_17'),
        'password_17' => env('PROSTOR_SMS_PASSWORD_17'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_PILIGHT_BOT_TOKEN', null)
    ],

];
