<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Event\BaseEvent;

final class AgentExecutorForcedStop extends BaseEvent
{
    public function __construct(
        public Steps $intermediateSteps,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
