<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter;

use Vexo\Document\Documents;

interface TextSplitter
{
    public function splitDocuments(Documents $document): Documents;

    /**
     * @return array<string>
     */
    public function split(string $text): array;
}
