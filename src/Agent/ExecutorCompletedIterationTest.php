<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(ExecutorCompletedIteration::class)]
final class ExecutorCompletedIterationTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $context = new Context();
        $previousSteps = new Steps();
        $event = new ExecutorCompletedIteration($context, $previousSteps);

        $this->assertSame($context, $event->context());
        $this->assertSame($previousSteps, $event->previousSteps());
    }
}
