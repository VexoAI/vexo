<?php

declare(strict_types=1);

namespace Vexo\Document;

use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

final class Document
{
    public function __construct(
        private readonly string $contents,
        private readonly MetadataContract $metadata = new Metadata()
    ) {
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function metadata(): MetadataContract
    {
        return $this->metadata;
    }
}
