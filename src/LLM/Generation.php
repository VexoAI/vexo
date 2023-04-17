<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

final class Generation
{
    public function __construct(private string $text)
    {
    }

    public function text(): string
    {
        return $this->text;
    }
}