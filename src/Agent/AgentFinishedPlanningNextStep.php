<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;
use Vexo\Contract\Event\BaseEvent;

final class AgentFinishedPlanningNextStep extends BaseEvent
{
    public function __construct(
        public Input $input,
        public Steps $intermediateSteps,
        public Step $step
    ) {
    }
}
