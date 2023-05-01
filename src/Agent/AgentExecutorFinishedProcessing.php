<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Event\BaseEvent;

final class AgentExecutorFinishedProcessing extends BaseEvent
{
    public function __construct(
        public array $results,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
