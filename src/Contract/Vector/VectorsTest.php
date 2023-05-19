<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Vectors::class)]
final class VectorsTest extends TestCase
{
    public function testGetType(): void
    {
        $vectors = new Vectors();

        $this->assertSame(Vector::class, $vectors->getType());
    }
}
