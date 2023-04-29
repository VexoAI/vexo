<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Event\SomethingHappened;

final class AgentExecutorForcedStop extends SomethingHappened
{
    public function __construct(
        public Steps $intermediateSteps,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
