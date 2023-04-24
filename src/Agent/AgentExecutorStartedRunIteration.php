<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\SomethingHappened;

final class AgentExecutorStartedRunIteration extends SomethingHappened
{
    public function __construct(
        public Steps $intermediateSteps,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
