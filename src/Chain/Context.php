<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Ramsey\Collection\Map\AbstractMap;

/**
 * @extends AbstractMap<string, mixed>
 */
final class Context extends AbstractMap
{
    public function get(
        int|string $key,
        mixed $defaultValue = null
    ): mixed {
        $key = (string) $key;

        if ($defaultValue === null && ! $this->containsKey($key)) {
            throw FailedToGetContextValue::with($key, $this->keys());
        }

        return $this[$key] ?? $defaultValue;
    }

    public function put(int|string $key, mixed $value): mixed
    {
        $key = (string) $key;

        $previousValue = $this[$key] ?? null;
        $this[$key] = $value;

        return $previousValue;
    }
}
