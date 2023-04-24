<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Steps::class)]
final class StepsTest extends TestCase
{
    public function testGetType(): void
    {
        $steps = new Steps();

        $this->assertSame(Step::class, $steps->getType());
    }
}
