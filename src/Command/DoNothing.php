<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use Assert\Assertion as Ensure;

final class DoNothing implements Command
{
    public static function fromArray(array $arguments): Command
    {
        Ensure::keyExists($arguments, 'reason');

        return new DoNothing($arguments['reason']);
    }

    public function __construct(public readonly string $reason)
    {
    }

    public function arguments(): array
    {
        return ['reason' => $this->reason];
    }
}