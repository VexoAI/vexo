<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Event\SomethingHappened;

final class AgentExecutorFinishedProcessing extends SomethingHappened
{
    public function __construct(
        public array $results,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
