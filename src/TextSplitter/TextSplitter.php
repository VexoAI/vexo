<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

interface TextSplitter
{
    public function split(string $text): array;
}
