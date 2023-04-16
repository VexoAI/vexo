<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

final class SimpleInput implements Input
{
    public function __construct(private array $data)
    {
    }

    public function data(): array
    {
        return $this->data;
    }
}