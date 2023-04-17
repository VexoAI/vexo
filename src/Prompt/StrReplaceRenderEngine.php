<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

final class StrReplaceRenderEngine implements RenderEngine
{
    public function render(string $template, array $values): Prompt
    {
        return new Prompt(
            str_replace(
                array_map(fn (string $key) => '{{' . $key . '}}', array_keys($values)),
                array_values($values),
                $template
            )
        );
    }
}