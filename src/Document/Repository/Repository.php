<?php

declare(strict_types=1);

namespace Vexo\Document\Repository;

use Vexo\Document\Document;
use Vexo\Document\Documents;

interface Repository
{
    public function persist(Document $document): void;

    public function search(string $query, int $maxResults = 4): Documents;
}
