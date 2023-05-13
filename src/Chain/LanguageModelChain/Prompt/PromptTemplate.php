<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

interface PromptTemplate
{
    public function render(array $values): Prompt;

    public function variables(): array;
}
