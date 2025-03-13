## 
## Description

- command for sync email templates table in database
- all email templates in seeder file
- global and personal variables for each email template
- storage location of your choice (in blade templates or database)
- text for custom variables via lexicon file
- wrapper template for all email templates (blade)

## Install

Open file **bootstrap/providers.php** and connect the provider from the package (optional, using laravel discovered package system by default)

```
\Crow\LaravelEmailTemplate\Providers\EmailTemplateServiceProvider::class,
```

**2.** Run commands

For creating config file

```
php artisan vendor:publish --provider="Crow\LaravelEmailTemplate\Providers\EmailTemplateServiceProvider" --tag=config
```

For creating lexicon file

```
php artisan vendor:publish --provider="Crow\LaravelEmailTemplate\Providers\EmailTemplateServiceProvider" --tag=lang
```

For creating migration file

```
php artisan email:template:publish --tag=migration
```

For generate table

```
php artisan migrate
```

## Next steps install

**1.** Create seeder file if not exists for email templates.
In the created seed file, you need to add a static method (for example, `public static function data()`).
The method must return an array of standard to fill the database

**2.** Open config file `config/email_template.php` and add this class and method in exists parameters

```
'data' => [
    'class' => \Database\Seeders\EmailTemplateTableSeeder::class,
    'method' => 'data',
],
```

**3** Example content seeder file `database/seeders/EmailTemplateTableSeeder.php`

```
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailTemplateTableSeeder extends Seeder
{
    public static function data(): array
    {
        return [
            [
                'id' => 1,
                'type' => 'user_registration',
                'title' => 'title 1',
                'subject' => 'subject 1',
                'body' => 'body 1',
                'is_active' => true,
                'created_at' => '2021-01-05 15:00:00',
                'updated_at' => '2021-01-05 15:00:00',
            ],
            [
                'id' => 2,
                'type' => 'user_activate',
                'title' => 'title 2',
                'subject' => 'subject 2',
                'body' => 'body 2',
                'is_active' => true,
                'created_at' => '2021-01-05 15:00:00',
                'updated_at' => '2021-01-05 15:00:00',
            ],
        ];
    }
}
```

## Command for sync email templates

```
php artisan email:template:sync
```

## Usage

Eloquent model for use.

```
\Crow\LaravelEmailTemplate\Models\EmailTemplate
```

You can use helper `\Crow\LaravelEmailTemplate\Helpers\EmailTemplateHelper` or service `\Crow\LaravelEmailTemplate\Services\EmailTemplateService`.

further we will use the variable **$emailTemplate** as a model of Email Template

#### Email template parse short codes

```
$emailTemplate = \Crow\LaravelEmailTemplate\Models\EmailTemplate::query()->where('type', '=', 'user_registration')->first();
$variables = ['var1' => 'content 1']; // variable list
$wrapper = true; // (optional, default: true) use wrapper for email template
$withCopy = true; // (optional, default: true) use copy email template for render
$emailTemplate = EmailTemplateHelper::render($emailTemplate, $variables, $wrapper, $withCopy);
return $emailTemplate;
```

#### Reset config to default

```
EmailTemplateHelper::reset();
```

#### Override email template wrapper

Blade template are used

```
EmailTemplateHelper::setWrapper('email-template.wrapper');
```

## The procedure of adding new common variable

Variables will be valid in all email templates

1. Open file **config/email_template.php** and add custom variable in section **variables - global**, example:

```
'variables' => [
    'global' => [
        'site_name' => \App\CustomEmailTemplateVariableSiteName::class,
    ],
    ...
```

2. Create class **\App\CustomEmailTemplateVariableSiteName**
3. The class must be an implementation of the interface **Crow\LaravelEmailTemplate\EmailTemplateVariableInterface**
4. The class must contain a public method **content(EmailTemplate $emailTemplate): ?string**

Full example file

```
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
```

## Procedure for adding template variable descriptions (optional)

These lists do not affect the variables used in templates, this may only be required to display a list of available variables for a specific email template type.

1. Open file **config/email_template.php** and add custom variable in section **variables - type**, example:

```
'type' => [
     'user_registration' => [
         'email',
         'password',
     ],
 ]
```

2. Add text for variables in to lexicon file **lang/LANG_KEY/email_template.php**, section **variables - common** or **variables - type**
3. Get a list of variables for a specific email template

```
$emailTemplate = \Crow\LaravelEmailTemplate\Models\EmailTemplate::query()->where('type', '=', 'user_registration')->first();
$emailTemplate->variables; // Collection
$emailTemplate->variables[0]->key;
$emailTemplate->variables[0]->description;
```
