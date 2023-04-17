<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Pragmatist\Assistant\Prompt\Prompt;

interface LLM
{
    /**
     * @param Prompt[] $prompts The prompts to generate a response for.
     */
    public function generate(Prompt $prompt, Prompt ...$prompts): Response;
}