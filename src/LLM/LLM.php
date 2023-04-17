<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Pragmatist\Assistant\Prompt\Prompt;

interface LLM
{
    /**
     * @param Prompt[] $prompt The prompts to generate a response for.
     */
    public function generate(Prompt ...$prompt): Response;
}