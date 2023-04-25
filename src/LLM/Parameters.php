<?php

declare(strict_types=1);

namespace Vexo\LLM;

final class Parameters
{
    public function __construct(private array $parameters)
    {
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    public function withDefaults(array $defaultParameters): self
    {
        return new self(array_merge($defaultParameters, $this->parameters));
    }
}
