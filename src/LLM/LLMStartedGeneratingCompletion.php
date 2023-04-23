<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\SomethingHappened;

final class LLMStartedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops
    ) {
    }
}
