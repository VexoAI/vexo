<?php

declare(strict_types=1);

namespace Vexo\Contract\Metadata\Implementation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

#[CoversClass(Metadatas::class)]
final class MetadatasTest extends TestCase
{
    public function testGetType(): void
    {
        $documents = new Metadatas();

        $this->assertSame(MetadataContract::class, $documents->getType());
    }
}
