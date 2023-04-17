<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

interface Renderer
{
    /**
     * @param string $template The template to render.
     * @param array $values Associative array of names and values to render into the template.
     * @return Prompt
     */
    public function render(string $template, array $values): Prompt;
}