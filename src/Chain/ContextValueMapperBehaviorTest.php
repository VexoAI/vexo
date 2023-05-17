<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContextValueMapperBehavior::class)]
final class ContextValueMapperBehaviorTest extends TestCase
{
    public function testPut(): void
    {
        $context = new Context();
        $stub = new StubContextValueMapperBehavior();

        $stub->putX($context, 'foo', 'bar');

        $this->assertEquals('bar', $context->get('foo'));
    }

    public function testGet(): void
    {
        $context = new Context(['foo' => 'bar']);
        $stub = new StubContextValueMapperBehavior();

        $this->assertEquals('bar', $stub->getX($context, 'foo'));
    }

    public function testGetThrowsExceptionIfKeyDoesNotExist(): void
    {
        $context = new Context(['foo' => 'bar']);
        $stub = new StubContextValueMapperBehavior();

        $this->expectException(FailedToGetContextValue::class);
        $this->expectExceptionMessage('Failed to get context value "baz". Available values: foo');

        $stub->getX($context, 'baz');
    }
}

final class StubContextValueMapperBehavior
{
    use ContextValueMapperBehavior;

    public function getX(Context $context, string $key, mixed $default = null): mixed
    {
        return $this->get($context, $key, $default);
    }

    public function putX(Context $context, string $key, mixed $value): void
    {
        $this->put($context, $key, $value);
    }
}
