<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\Chain\Input;
use Vexo\Weave\SomethingHappened;

final class AgentStartedPlanningNextStep extends SomethingHappened
{
    public function __construct(
        public Input $input,
        public array $intermediateSteps
    ) {
    }
}
