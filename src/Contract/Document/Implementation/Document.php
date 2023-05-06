<?php

declare(strict_types=1);

namespace Vexo\Contract\Document\Implementation;

use Ramsey\Collection\Map\AbstractMap;
use Vexo\Contract\Document\Document as DocumentContract;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

final class Document extends AbstractMap implements DocumentContract
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
