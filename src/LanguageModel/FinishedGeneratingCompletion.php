<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Event\BaseEvent;
use Vexo\Prompt\Prompt;

final class FinishedGeneratingCompletion extends BaseEvent
{
    public function __construct(
        public Prompt $prompt,
        public array $stops,
        public Completions $completions
    ) {
    }
}
