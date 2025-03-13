<?php

namespace Crow\LaravelEmailTemplate\Services;

use Closure;
use Crow\LaravelEmailTemplate\Exceptions\EmailTemplateVariableException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Crow\LaravelEmailTemplate\Exceptions\EmailTemplateWrapperException;
use Crow\LaravelEmailTemplate\Models\EmailTemplate;
use Throwable;

class EmailTemplateService
{
    private ?Closure $parseFunction = null;
    private ?array $parseFields = null;
    private ?string $parseTagOpen = null;
    private ?string $parseTagClose = null;
    private ?Collection $globalVariables = null;
    private ?string $emailWrapper = null;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): EmailTemplateService
    {
        $this->parseFunction = config('email_template.variable_parser.function');
        $this->parseFields = config('email_template.variable_parser.fields');
        $this->parseTagOpen = config('email_template.variable_parser.tag_open');
        $this->parseTagClose = config('email_template.variable_parser.tag_close');
        $this->emailWrapper = config('email_template.wrapper');
        $this->globalVariables = collect(config('email_template.variables.global'));

        return $this;
    }

    public function setWrapper(?string $emailWrapper): EmailTemplateService
    {
        $this->emailWrapper = $emailWrapper;
        return $this;
    }

    public function render(
        EmailTemplate $emailTemplate,
        array $variables,
        bool $wrapper = true,
        bool $withCopy = true
    ): EmailTemplate {
        if ($withCopy) {
            $emailTemplate = clone $emailTemplate;
        }

        if (!is_array($this->parseFields) || empty($this->parseFields)) {
            return $emailTemplate;
        }

        if ($emailTemplate->is_blade_template && $emailTemplate->blade_template) {
            $emailTemplate->body = view($emailTemplate->blade_template, $variables);
        }

        foreach ($this->parseFields as $field) {
            if (!$emailTemplate->$field) {
                continue;
            }

            $emailTemplate->$field = $this->renderContent($emailTemplate, $emailTemplate->$field, $variables);
        }

        if ($wrapper) {
            $this->renderWrapper($emailTemplate);
        }

        return $emailTemplate;
    }

    private function renderContentCommonVariables(EmailTemplate $emailTemplate, string $content): string
    {
        if (!$this->globalVariables->count()) {
            return $content;
        }

        $data = [];
        foreach ($this->globalVariables as $key => $className) {
            try {
                $variable = app($className);

                $key = sprintf(
                    '%s%s%s',
                    $this->parseTagOpen,
                    $key,
                    $this->parseTagClose
                );
                $data[$key] = $variable->content($emailTemplate);
            } catch (Throwable $e) {
                throw new EmailTemplateVariableException($e->getMessage());
            }
        }

        return strtr($content, $data);
    }

    private function renderContent(EmailTemplate $emailTemplate, string $content, array $variables): ?string
    {
        $content = $this->renderContentCommonVariables($emailTemplate, $content);

        if (is_callable($this->parseFunction)) {
            $function = $this->parseFunction;
            $content = $function($emailTemplate, $content, $variables);
            unset($function);
        }

        return $content;
    }

    private function renderWrapper(EmailTemplate $emailTemplate): void
    {
        if (!$this->emailWrapper) {
            return;
        }

        try {
            $emailTemplate->body = View::make(
                $this->emailWrapper,
                compact('emailTemplate')
            )->render();
        } catch (Throwable $e) {
            throw new EmailTemplateWrapperException($e->getMessage());
        }
    }
}
