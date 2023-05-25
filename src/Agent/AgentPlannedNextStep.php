<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;
use Vexo\Contract\Event;

final class AgentPlannedNextStep implements Event
{
    public function __construct(
        private readonly Context $context,
        private readonly Steps $previousSteps,
        private readonly Step $nextStep
    ) {
    }

    public function context(): Context
    {
        return $this->context;
    }

    public function previousSteps(): Steps
    {
        return $this->previousSteps;
    }

    public function nextStep(): Step
    {
        return $this->nextStep;
    }
}
