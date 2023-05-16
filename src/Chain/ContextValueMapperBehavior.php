<?php

declare(strict_types=1);

namespace Vexo\Chain;

trait ContextValueMapperBehavior
{
    private function get(Context $context, string $key, mixed $default = null): mixed
    {
        return $context->get($this->inputMap[$key] ?? $key, $default);
    }

    private function put(Context $context, string $key, mixed $value): void
    {
        $context->put($this->outputMap[$key] ?? $key, $value);
    }
}
