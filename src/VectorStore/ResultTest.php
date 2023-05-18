<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;

#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    private Result $result;

    protected function setUp(): void
    {
        $this->result = new Result(
            vector: new Vector([0.1, -0.1]),
            metadata: new Metadata(['foo' => 'bar']),
            score: 0.5
        );
    }

    public function testVector(): void
    {
        $this->assertSame([0.1, -0.1], $this->result->vector()->toArray());
    }

    public function testMetadata(): void
    {
        $this->assertSame('bar', $this->result->metadata()->get('foo'));
    }

    public function testScore(): void
    {
        $this->assertSame(0.5, $this->result->score());
    }
}
