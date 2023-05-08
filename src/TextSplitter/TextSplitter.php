<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Vexo\Contract\Document\Documents;

interface TextSplitter
{
    public function splitDocuments(Documents $document): Documents;

    public function split(string $text): array;
}
