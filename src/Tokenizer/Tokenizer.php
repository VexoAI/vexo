<?php

declare(strict_types=1);

namespace Vexo\Tokenizer;

interface Tokenizer
{
    public function encode(string $text): array;

    public function decode(array $tokens): string;
}
