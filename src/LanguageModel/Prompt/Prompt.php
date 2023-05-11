<?php

declare(strict_types=1);

namespace Vexo\LanguageModel\Prompt;

final class Prompt implements \Stringable
{
    public function __construct(
        private readonly string $text
    ) {
    }

    public function text(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->text;
    }
}
