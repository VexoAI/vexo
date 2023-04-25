<?php

declare(strict_types=1);

namespace Vexo\LLM;

use Vexo\Prompt\Prompt;

interface LLM
{
    /**
     * @param Prompt $prompt the prompt to generate a response for
     * @param string $stops  the stop tokens to use for the generation
     */
    public function generate(Prompt $prompt, string ...$stops): Response;
}
