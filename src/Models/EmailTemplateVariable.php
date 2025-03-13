<?php

namespace Crow\LaravelEmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

/**
 * @property string $type
 * @property string $key
 * @property string $description
 */
class EmailTemplateVariable extends Model
{
    public static function register(
        string $type,
        string $key
    ): self {
        $model = new self();
        $model->type = $type;
        $model->key = $key;

        return $model;
    }

    /**
     * @return string
     */
    public function getKeyAttribute()
    {
        return sprintf(
            '%s%s%s',
            config('email_template.variable_parser.tag_open'),
            $this->attributes['key'],
            config('email_template.variable_parser.tag_close')
        );
    }

    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $key = sprintf(
            'email_template.variables.type.%s.%s',
            $this->type,
            $this->attributes['key']
        );
        if (Lang::has($key)) {
            return Lang::get($key);
        }

        $key = sprintf(
            'email_template.variables.common.%s',
            $this->attributes['key']
        );

        if (Lang::has($key)) {
            return Lang::get($key);
        }

        return '';
    }
}
