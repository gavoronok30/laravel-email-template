<?php

namespace Crow\LaravelEmailTemplate\Helpers;

use Illuminate\Support\Facades\Config;

class EmailTemplateVariableHelper
{
    public static function parse(string $content, array $variables): ?string
    {
        $open = Config::get('email_template.variable_parser.tag_open');
        $close = Config::get('email_template.variable_parser.tag_close');

        $replace = [];
        foreach ($variables as $key => $value) {
            $key = sprintf(
                '%s%s%s',
                $open,
                $key,
                $close
            );
            $replace[$key] = (string)$value;
        }

        return strtr($content, $replace);
    }
}
