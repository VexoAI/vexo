<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class AgentExecutorStartedProcessing implements Event
{
    public function __construct(
        public Context $context
    ) {
    }
}
