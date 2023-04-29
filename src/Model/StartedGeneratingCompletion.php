<?php

declare(strict_types=1);

namespace Vexo\Model;

use Vexo\Event\SomethingHappened;
use Vexo\Prompt\Prompt;

final class StartedGeneratingCompletion extends SomethingHappened
{
    public function __construct(
        public Prompt $prompt,
        public array $stops
    ) {
    }
}
