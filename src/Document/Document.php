<?php

declare(strict_types=1);

namespace Vexo\Document;

final class Document
{
    public function __construct(
        private readonly string $contents,
        private readonly Metadata $metadata = new Metadata()
    ) {
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }
}
