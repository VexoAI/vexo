<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

final class Finish
{
    public function __construct(
        private array $results = []
    ) {
    }

    public function results(): array
    {
        return $this->results;
    }
}
