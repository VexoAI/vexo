<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

final class StrReplaceRenderer implements Renderer
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