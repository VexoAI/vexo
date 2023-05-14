<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class ExecutorCompletedIteration implements Event
{
    public function __construct(
        private readonly Context $context,
        private readonly Steps $previousSteps
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
}
