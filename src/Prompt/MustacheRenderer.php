<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

final class MustacheRenderer implements Renderer
{
    public function __construct(
        private \Mustache_Engine $mustache
    ) {
    }

    public function render(string $template, array $values): Prompt
    {
        return new Prompt($this->mustache->render($template, $values));
    }
}