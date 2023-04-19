<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

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
