<?php

namespace Crow\LaravelEmailTemplate\Helpers;

use Crow\LaravelEmailTemplate\Services\EmailTemplateService;
use Illuminate\Support\Facades\Facade;
use Crow\LaravelEmailTemplate\Models\EmailTemplate;

/**
 * @method static EmailTemplateService reset()
 * @method static EmailTemplateService setWrapper(?string $emailWrapper)
 * @method static EmailTemplate render(EmailTemplate $emailTemplate, array $variables, bool $wrapper = true, bool $withCopy = true)
 */
class EmailTemplateHelper extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return EmailTemplateHelperHandler::class;
    }
}
