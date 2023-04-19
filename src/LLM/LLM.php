<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompts;

interface LLM
{
    /**
     * @param Prompts $prompts The prompts to generate a response for.
     * @param string[] $stop The stop tokens to use for the generation.
     */
    public function generate(Prompts $prompts, string ...$stops): Response;
}