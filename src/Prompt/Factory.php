<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

final class Factory
{
    public function __construct(
        private RenderEngine $engine
    ) {
    }

    public function simple(string $template): Prompt
    {
        return new SimplePrompt($this->engine, $template);
    }
}