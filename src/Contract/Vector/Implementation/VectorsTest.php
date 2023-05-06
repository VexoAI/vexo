<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector\Implementation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Vector\Vector as VectorContract;

#[CoversClass(Vectors::class)]
final class VectorsTest extends TestCase
{
    public function testGetType(): void
    {
        $documents = new Vectors();

        $this->assertSame(VectorContract::class, $documents->getType());
    }
}
