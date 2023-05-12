<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Contract\Event\Event;

final class AgentExecutorStartedRunIteration implements Event
{
    public function __construct(
        public Steps $intermediateSteps,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
