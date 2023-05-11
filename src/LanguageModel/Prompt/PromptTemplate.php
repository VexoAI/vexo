<?php

declare(strict_types=1);

namespace Vexo\LanguageModel\Prompt;

interface PromptTemplate
{
    public function render(array $values): Prompt;
}
