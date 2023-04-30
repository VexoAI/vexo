<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

interface TextExtractor
{
    public function extract(string $contents): string;
}
