<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;
use Vexo\Weave\SomethingHappened;

#[CodeCoverageIgnore]
final class AgentExecutorFinishedProcessing extends SomethingHappened
{
    public function __construct(
        public array $results,
        public int $iterations,
        public int $timeElapsed
    ) {
    }
}
