<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Finish
{
    public function __construct(
        private readonly array $results = []
    ) {
    }

    public function results(): array
    {
        return $this->results;
    }
}
