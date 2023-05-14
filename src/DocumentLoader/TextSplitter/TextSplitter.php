<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader\TextSplitter;

use Vexo\Contract\Document\Documents;

interface TextSplitter
{
    public function splitDocuments(Documents $document): Documents;

    /**
     * @return array<string>
     */
    public function split(string $text): array;
}
