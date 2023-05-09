<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Document;

interface WritableVectorStore
{
    public function add(Document $document): void;
}
