<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader\TextSplitter\Tokenizer;

interface Tokenizer
{
    /**
     * @return array<int>
     */
    public function encode(string $text): array;

    /**
     * @param array<int> $tokens
     */
    public function decode(array $tokens): string;
}
