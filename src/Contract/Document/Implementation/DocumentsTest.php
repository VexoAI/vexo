<?php

declare(strict_types=1);

namespace Vexo\Contract\Document\Implementation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Document as DocumentContract;

#[CoversClass(Documents::class)]
final class DocumentsTest extends TestCase
{
    public function testGetType(): void
    {
        $documents = new Documents();

        $this->assertSame(DocumentContract::class, $documents->getType());
    }
}
