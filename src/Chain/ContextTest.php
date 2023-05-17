<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Context::class)]
final class ContextTest extends TestCase
{
    public function testGet(): void
    {
        $context = new Context(['foo' => 'bar']);

        $this->assertEquals('bar', $context->get('foo'));
    }

    public function testGetMissingKeyWithDefaultValue(): void
    {
        $context = new Context();

        $this->assertEquals('bar', $context->get('non-existent', defaultValue: 'bar'));
    }

    public function testGetMissingKeyWithoutDefaultValue(): void
    {
        $context = new Context();

        $this->expectException(FailedToGetContextValue::class);
        $context->get('non-existent');
    }

    public function testPut(): void
    {
        $context = new Context(['foo' => 'bar']);

        $this->assertEquals('bar', $context->put('foo', 'baz'));
        $this->assertNull($context->put('non-existent', 'qux'));

        $this->assertEquals('baz', $context->get('foo'));
        $this->assertEquals('qux', $context->get('non-existent'));
    }
}
