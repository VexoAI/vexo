<?php

declare(strict_types=1);

namespace Vexo\Chain;

trait ContextValueMapperBehavior
{
    private function get(Context $context, string $key, mixed $default = null): mixed
    {
        $key = $this->inputMap[$key] ?? $key;

        if ($default === null && ! $context->containsKey($key)) {
            throw FailedToGetContextValue::with($key, $context);
        }

        return $context->get($key, $default);
    }

    private function put(Context $context, string $key, mixed $value): void
    {
        $context->put($this->outputMap[$key] ?? $key, $value);
    }
}
