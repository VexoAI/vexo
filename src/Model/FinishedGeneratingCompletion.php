<?php

declare(strict_types=1);

namespace Vexo\Model;

use Vexo\Prompt\Prompt;
use Vexo\SomethingHappened;

final class FinishedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops,
        public Completions $completions
    ) {
    }
}
