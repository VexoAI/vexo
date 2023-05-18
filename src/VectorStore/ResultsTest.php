<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Results::class)]
final class ResultsTest extends TestCase
{
    public function testGetType(): void
    {
        $results = new Results();

        $this->assertSame(Result::class, $results->getType());
    }
}
