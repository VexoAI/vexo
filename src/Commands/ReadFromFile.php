<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;

final class ReadFromFile implements Command
{
    public static function fromArray(array $arguments): Command
    {
        Ensure::keyExists($arguments, 'file');
        Ensure::notEmpty($arguments['file']);

        return new ReadFromFile($arguments['file']);
    }

    public function __construct(
        public readonly string $file
    ) {
    }

    public function arguments(): array
    {
        return ['file' => $this->file];
    }
}