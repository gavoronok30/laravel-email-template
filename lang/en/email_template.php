<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default title for each email template
    |--------------------------------------------------------------------------
    | key: email template type
    | value: text
    | example:
    | [
    |     'user_registration' => 'User registration email',
    | ]
    */
    'title' => [
        'user_registration' => 'User registration email',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default description for each email template
    |--------------------------------------------------------------------------
    | key: email template type
    | value: text
    | example:
    | [
    |     'user_registration' => 'Letter to send after user registration',
    | ]
    */
    'description' => [
        'user_registration' => 'Letter to send after user registration',
    ],

    'variables' => [
        /*
        |--------------------------------------------------------------------------
        | Description of variables for all types of email template
        |--------------------------------------------------------------------------
        */
        'common' => [
            'site_name' => 'Site name',
            'email' => 'User email',
            /* other variables */
        ],
        /*
        |--------------------------------------------------------------------------
        | Description of variables for email template by type
        |--------------------------------------------------------------------------
        | Variables specified here take precedence over those specified in the section above "common"
        */
        'type' => [
            'user_registration' => [
                'password' => 'User password',
            ],
            'user_activate' => [
                'email' => 'Main user email',
                'other_variable' => 'Variable',
            ]
            /* other types */
        ]
    ]
];
