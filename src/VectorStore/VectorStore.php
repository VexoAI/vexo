<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;

interface VectorStore
{
    public function add(Vector $vector, Metadata $metadata): void;

    public function search(string $query, int $maxResults = 4): Results;
}
