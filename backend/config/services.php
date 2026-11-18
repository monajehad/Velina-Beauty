<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'number' => env('WHATSAPP_NUMBER'),

        // Server-side sending of the order PDF (see App\Services\WhatsAppSender).
        // Leave WHATSAPP_DRIVER empty to keep the normal "open WhatsApp" button.
        'driver' => env('WHATSAPP_DRIVER'),
        'meta' => [
            'token' => env('WHATSAPP_META_TOKEN'),
            'phone_number_id' => env('WHATSAPP_META_PHONE_NUMBER_ID'),
            'version' => env('WHATSAPP_META_VERSION', 'v21.0'),
            'template' => env('WHATSAPP_META_TEMPLATE'),
            'template_lang' => env('WHATSAPP_META_TEMPLATE_LANG', 'en'),
        ],
        'ultramsg' => [
            'instance' => env('WHATSAPP_ULTRAMSG_INSTANCE'),
            'token' => env('WHATSAPP_ULTRAMSG_TOKEN'),
        ],
    ],

];