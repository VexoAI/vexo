<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

final class Parameters
{
    public function __construct(private array $parameters)
    {
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    public function withDefaults(array $defaultParameters): Parameters
    {
        return new Parameters(array_merge($defaultParameters, $this->parameters));
    }
}
