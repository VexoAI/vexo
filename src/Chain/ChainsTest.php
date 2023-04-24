<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Chains::class)]
final class ChainsTest extends TestCase
{
    public function testGetType(): void
    {
        $chains = new Chains();

        $this->assertSame(Chain::class, $chains->getType());
    }
}
