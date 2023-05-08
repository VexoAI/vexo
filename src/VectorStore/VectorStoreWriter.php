<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Document;

interface VectorStoreWriter
{
    public function add(Document $document): void;
}
