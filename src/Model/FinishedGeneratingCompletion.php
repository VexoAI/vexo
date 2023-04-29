<?php

declare(strict_types=1);

namespace Vexo\Model;

use Vexo\Event\SomethingHappened;
use Vexo\Prompt\Prompt;

final class FinishedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops,
        public Completions $completions
    ) {
    }
}
