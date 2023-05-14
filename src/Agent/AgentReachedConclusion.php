<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class AgentReachedConclusion implements Event
{
    public function __construct(
        private readonly Context $context,
        private readonly Steps $previousSteps,
        private readonly Conclusion $conclusion
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

    public function conclusion(): Conclusion
    {
        return $this->conclusion;
    }
}
