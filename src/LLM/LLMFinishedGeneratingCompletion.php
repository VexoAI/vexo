<?php

declare(strict_types=1);

namespace Vexo\LLM;

use Vexo\Prompt\Prompt;
use Vexo\SomethingHappened;

final class LLMFinishedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops,
        public Generations $generations
    ) {
    }
}
