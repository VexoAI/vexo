<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;
use Vexo\Contract\Event\BaseEvent;

final class AgentFinishedPlanningNextStep extends BaseEvent
{
    public function __construct(
        public Context $context,
        public Steps $intermediateSteps,
        public Step $step
    ) {
    }
}
