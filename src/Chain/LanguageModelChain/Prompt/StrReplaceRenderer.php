<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Vexo\Chain\Context;

final class StrReplaceRenderer implements Renderer
{
    public function __construct(private readonly string $templateBody)
    {
    }

    public function render(Context $context): string
    {
        return str_replace(
            array_map(fn ($variable) => "{{{$variable}}}", $context->keys()),
            array_replace(array_flip($context->keys()), $context->toArray()),
            $this->templateBody
        );
    }
}
