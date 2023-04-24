<?php

declare(strict_types=1);

namespace Vexo\LLM;

final class ResponseMetadata
{
    public function __construct(private array $values = [])
    {
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
