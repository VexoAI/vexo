<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

interface PromptTemplate
{
    public function render(array $values): Prompt;
}
