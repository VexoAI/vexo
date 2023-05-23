<?php

declare(strict_types=1);

namespace Vexo\Document\Retriever;

use Vexo\Document\Documents;

interface Retriever
{
    public function retrieve(string $query, int $maxResults = 4): Documents;
}
