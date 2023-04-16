<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;

final class WriteToFile implements Command
{
    public static function fromArray(array $arguments): Command
    {
        Ensure::keyExists($arguments, 'file');
        Ensure::notEmpty($arguments['file']);

        Ensure::keyExists($arguments, 'contents');
        Ensure::notEmpty($arguments['contents']);

        return new WriteToFile($arguments['file'], $arguments['contents']);
    }

    public function __construct(
        public readonly string $file,
        public readonly string $contents
    ) {
    }

    public function arguments(): array
    {
        return ['file' => $this->file, 'contents' => $this->contents];
    }
}