<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Contract\Event\BaseEvent;
use Vexo\Prompt\Prompt;

final class StartedGeneratingCompletion extends BaseEvent
{
    public function __construct(
        public Prompt $prompt,
        public array $stops
    ) {
    }
}
