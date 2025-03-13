<?php

namespace Crow\LaravelEmailTemplate\Interfaces;

use Crow\LaravelEmailTemplate\Models\EmailTemplate;

interface EmailTemplateVariableInterface
{
    public function content(EmailTemplate $emailTemplate): ?string;
}
