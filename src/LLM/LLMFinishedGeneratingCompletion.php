<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\SomethingHappened;

final class LLMFinishedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops,
        public Generations $generations
    ) {
    }
}
