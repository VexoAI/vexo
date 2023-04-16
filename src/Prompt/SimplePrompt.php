<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

final class SimplePrompt implements Prompt
{
    public function __construct(
        private RenderEngine $engine,
        private string $template
    ) {
    }

    public function render(array $values): string
    {
        return $this->engine->render($this->template, $values);
    }

    public function template(): string
    {
        return $this->template;
    }
}