<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\SomethingHappened;

final class AgentExecutorStartedRunIteration extends SomethingHappened
{
    /**
     * @param Step[] $intermediateSteps
     */
    public function __construct(
        public array $intermediateSteps,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
