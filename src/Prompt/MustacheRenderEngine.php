<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

final class MustacheRenderEngine implements RenderEngine
{
    public function __construct(
        private \Mustache_Engine $mustache
    ) {
    }

    public function render(string $template, array $values): string
    {
        return $this->mustache->render($template, $values);
    }
}