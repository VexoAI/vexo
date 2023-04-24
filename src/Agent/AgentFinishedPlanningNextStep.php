<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;
use Vexo\SomethingHappened;

final class AgentFinishedPlanningNextStep extends SomethingHappened
{
    public function __construct(
        public Input $input,
        public array $intermediateSteps,
        public Step $step
    ) {
    }
}
