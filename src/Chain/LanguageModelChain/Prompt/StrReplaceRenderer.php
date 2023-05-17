<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Vexo\Chain\Context;
use Vexo\Chain\FailedToGetContextValue;

final class StrReplaceRenderer implements Renderer
{
    public function __construct(private readonly string $templateBody)
    {
    }

    public function render(Context $context): string
    {
        $rendered = str_replace(
            array_map(fn ($variable) => "{{{$variable}}}", $context->keys()),
            array_replace(array_flip($context->keys()), $context->toArray()),
            $this->templateBody
        );

        $matches = [];
        if (preg_match('/{{(?<key>\w+)}}/', $rendered, $matches)) {
            throw FailedToGetContextValue::with($matches['key'], $context->keys());
        }

        return $rendered;
    }
}
