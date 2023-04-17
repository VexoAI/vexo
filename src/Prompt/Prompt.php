<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

final class Prompt
{
    public function __construct(private string $text)
    {
    }

    public function text(): string
    {
        return $this->text;
    }
}