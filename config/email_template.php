<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    | Table name for email template
    */
    'table' => 'email_templates',

    /*
    |--------------------------------------------------------------------------
    | Data from seeder file
    |--------------------------------------------------------------------------
    | Required
    | class - class seeder file (example): Database\Seeders\EmailTemplateTableSeeder::class
    | method - static method with array of data with seeder format
    | Example seeder file database/seeders/EmailTemplateTableSeeder.php:
    | class EmailTemplateTableSeeder extends Seeder
    | {
    | ...
    |     public static function data()
    |     {
    |         return [
    |             [
    |                'id' => 1,
    |                'type' => 'user_registration',
    |                ...
    |            ],
    |            [
    |                'id' => 2,
    |                'type' => 'user_banned',
    |                ...
    |            ],
    |            ...
    |        ];
    |    }
    | }
    */
    'data' => [
        'class' => '',
        'method' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Variable parser
    |--------------------------------------------------------------------------
    | Settings for an internal or external variable parser
    */
    'variable_parser' => [
        /*
        |--------------------------------------------------------------------------
        | Open and close tag for extract of variable
        |--------------------------------------------------------------------------
        */
        'tag_open' => '[[',
        'tag_close' => ']]',

        /*
        |--------------------------------------------------------------------------
        | Parser for variables
        |--------------------------------------------------------------------------
        | If a parser is used, then it should be wrapped in an anonymous function
        | , and the anonymous function should return a data type of "string"
        | Example (default parser):
        | 'function' => function (\Crow\LaravelEmailTemplate\Models\EmailTemplate $emailTemplate, string $content, array $variables) {
        |     return \Crow\LaravelEmailTemplate\Helpers\EmailTemplateVariableHelper::parse($content, $variables);
        | },
        */
        'function' => function (\Crow\LaravelEmailTemplate\Models\EmailTemplate $emailTemplate, string $content, array $variables) {
            return \Crow\LaravelEmailTemplate\Helpers\EmailTemplateVariableHelper::parse($content, $variables);
        },

        /*
        |--------------------------------------------------------------------------
        | Fields for parsing
        |--------------------------------------------------------------------------
        | array [
        |     'subject',
        |     'body'
        | ]
        */

        'fields' => [
            'subject',
            'body'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Config for sync command
    |--------------------------------------------------------------------------
    | If the email template has the "is_custom" field set to "true"
    | , then that email template is ignored when updating and deleting
    */
    'sync' => [
        /*
        |--------------------------------------------------------------------------
        | Creating new email template if not exists to database
        |--------------------------------------------------------------------------
        | true - create
        | false - not create
        */
        'create' => true,
        /*
        |--------------------------------------------------------------------------
        | Deleting email template if email template been deleted in seeder file
        |--------------------------------------------------------------------------
        | true - delete
        | false - not delete
        */
        'delete' => true,
        /*
        |--------------------------------------------------------------------------
        | List of fields which has being updated auto at sync
        |--------------------------------------------------------------------------
        | array [
        |     'field_1',
        |     'field_2'
        | ]
        */
        'update_fields' => [
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Blade wrapper for all email templates
    |--------------------------------------------------------------------------
    | It is necessary to specify the blade template as a wrapper for all
    | email templates and add a special variable
    | in the template to insert the content of a specific email templates
    |
    | Example: email-template.wrapper
    | File (path): resources/views/email-template/wrapper.blade.php
    | Example content:
    | ...
    |     {!! $emailTemplate->body !!}
    | ...
    */
    'wrapper' => env('EMAIL_TEMPLATE_WRAPPER', ''),

    /*
    |--------------------------------------------------------------------------
    | Config variables for email templates
    |--------------------------------------------------------------------------
    */
    'variables' => [
        /*
        |--------------------------------------------------------------------------
        | Variables for all email template types
        |--------------------------------------------------------------------------
        | Example: [
        |     'global' => [
        |         'site_name' => \App\CustomEmailVariableSiteName::class,
        |         ...
        |     ]
        | ]
        | Example class:
            <?php

            namespace App;

            use Crow\LaravelEmailTemplate\EmailTemplateVariableInterface;
            use Crow\LaravelEmailTemplate\Models\EmailTemplate;

            class CustomEmailTemplateVariableSiteName implements EmailTemplateVariableInterface
            {
                public function content(EmailTemplate $emailTemplate): ?string
                {
                    return 'site name';
                }
            }
        */
        'global' => [
            /* list of variables for all email template types */
        ],
        /*
        |--------------------------------------------------------------------------
        | Variables for email template by types
        |--------------------------------------------------------------------------
        | Example: [
        |     'type' => [
        |         'user_registration' => [
        |             'email',
        |             'password',
        |             ...
        |         ],
        |         ...
        |     ]
        | ]
        */
        'type' => [
            /* list of variables for email template by types */
            'user_registration' => [
                'email',
                'password',
            ],
        ]
    ]
];
