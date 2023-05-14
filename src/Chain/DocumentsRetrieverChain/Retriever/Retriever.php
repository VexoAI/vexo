<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain\Retriever;

use Vexo\Contract\Document\Documents;

interface Retriever
{
    public function retrieve(string $query): Documents;
}
