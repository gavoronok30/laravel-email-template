<?php

namespace Crow\LaravelEmailTemplate\Helpers;

use Crow\LaravelEmailTemplate\Services\EmailTemplateService;
use Illuminate\Support\Collection;
use ReflectionMethod;

class EmailTemplateHelperHandler
{
    private Collection $methods;

    public function __construct(
        private EmailTemplateService $service
    ) {
        $this->methods = collect();
    }

    public function __call(string $methodName, array $arguments): mixed
    {
        if (!$this->methods->has($methodName)) {
            $this->methods->put(
                $methodName,
                (new ReflectionMethod($this->service, $methodName))->isPublic()
            );
        }

        if ($this->methods->get($methodName)) {
            return $this->service->$methodName(...$arguments);
        }

        return null;
    }
}
