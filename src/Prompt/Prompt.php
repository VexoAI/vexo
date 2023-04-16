<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

interface Prompt
{
    /**
     * @param array $values Associative array of names and values to render into the template.
     * @return string The rendered template.
     */
    public function render(array $values): string;
}