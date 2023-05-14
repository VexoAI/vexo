<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Conclusion::class)]
final class ConclusionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $conclusion = new Conclusion('Some thought', 'Some observation');

        $this->assertEquals('Some thought', $conclusion->thought());
        $this->assertEquals('Some observation', $conclusion->observation());
    }
}
