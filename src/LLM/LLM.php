<?php

declare(strict_types=1);

namespace Vexo\LLM;

use Vexo\Prompt\Prompt;

interface LLM
{
    public function generate(Prompt $prompt, string ...$stops): Response;
}
