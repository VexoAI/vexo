<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;
use Vexo\Contract\Event\BaseEvent;

final class AgentStartedPlanningNextStep extends BaseEvent
{
    public function __construct(
        public Input $input,
        public Steps $intermediateSteps
    ) {
    }
}
