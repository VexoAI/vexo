<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompt;

interface LLM
{
    /**
     * @param Prompt[] $prompt The prompts to generate a response for.
     */
    public function generate(Prompt ...$prompt): Response;
}