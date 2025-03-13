<?php

namespace Crow\LaravelEmailTemplate\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\LazyCollection;

/**
 * @property-read int $id
 * @property string $type
 * @property string $title
 * @property string $description
 * @property string $subject
 * @property string $body
 * @property bool $is_blade_template
 * @property string $blade_template
 * @property bool $is_custom
 * @property bool $is_active
 * @property array $properties
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property EmailTemplateVariable[] $variables
 *
 * @method static Builder|static query()
 * @method static Builder|static find(int $id)
 * @method static Builder|static findOrFail(int $id, $columns = ['*'])
 * @method static Builder|static first(array $columns = ['*'])
 * @method static Builder|static firstOrFail(array $columns = ['*'])
 * @method static Collection|static[] get(array $columns = ['*'])
 * @method static LazyCollection|static[] cursor(array $columns = ['*'])
 */
class EmailTemplate extends Model
{
    use HasFactory;

    protected $casts = [
        'is_blade_template' => 'boolean',
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'type',
        'title',
        'description',
        'subject',
        'body',
        'is_blade_template',
        'blade_template',
        'is_custom',
        'is_active',
        'properties',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = Config::get('email_template.table');

        parent::__construct($attributes);
    }

    /**
     * @return EmailTemplateVariable[]|Collection
     */
    public function getVariablesAttribute()
    {
        $data = collect();
        $variables = collect(Config::get('email_template.variables.global'))->keys();

        $configKey = sprintf(
            'email_template.variables.type.%s',
            $this->type
        );
        if (Config::get($configKey) && is_array(Config::get($configKey))) {
            $variables = $variables->merge(Config::get($configKey));
        }

        foreach ($variables as $variable) {
            $model = EmailTemplateVariable::register(
                $this->type,
                $variable
            );
            $data->push($model);
        }

        return $data;
    }

    public function getDescriptionAttribute(): ?string
    {
        if (isset($this->attributes['description']) && !empty($this->attributes['description'])) {
            return $this->attributes['description'];
        }

        $key = sprintf('email_template.description.%s', $this->type);

        return Lang::has($key) ? __($key) : null;
    }

    public function getTitleAttribute(): ?string
    {
        if (isset($this->attributes['title']) && !empty($this->attributes['title'])) {
            return $this->attributes['title'];
        }

        $key = sprintf('email_template.title.%s', $this->type);

        return Lang::has($key) ? __($key) : null;
    }
}
