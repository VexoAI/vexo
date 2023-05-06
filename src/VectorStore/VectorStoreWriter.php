<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;

interface VectorStoreWriter
{
    public function add(string $id, Vector $vector, Metadata $metadata): void;
}
