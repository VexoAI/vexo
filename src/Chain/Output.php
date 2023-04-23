<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

final class Output
{
    public function __construct(private array $data)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function data(): array
    {
        return $this->data;
    }
}
