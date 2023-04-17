<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use Assert\Assertion as Ensure;

final class Google implements Command
{
    public static function fromArray(array $arguments): Command
    {
        Ensure::keyExists($arguments, 'query');
        Ensure::notEmpty($arguments['query']);

        return new Google($arguments['query']);
    }

    public function __construct(public readonly string $query)
    {
    }

    public function arguments(): array
    {
        return ['query' => $this->query];
    }
}