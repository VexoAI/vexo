<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;
use Vexo\Event\SomethingHappened;

final class AgentExecutorStartedProcessing extends SomethingHappened
{
    public function __construct(
        public Input $input
    ) {
    }
}
