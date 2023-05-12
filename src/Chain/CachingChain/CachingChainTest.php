<?php

declare(strict_types=1);

namespace Vexo\Chain\CachingChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Vexo\Chain\Context;
use Vexo\Chain\Runner;

#[CoversClass(CachingChain::class)]
final class CachingChainTest extends TestCase
{
    public function testRun(): void
    {
        $cachingChain = new CachingChain(
            new RunnerStub(),
            new CacheStub(),
            ['query'],
            ['incrementor']
        );

        $context = new Context(['query' => 'Something amazing']);
        $cachingChain->run($context);

        $this->assertEquals(0, $context->get('incrementor'));

        $cachingChain->run($context);

        $this->assertEquals(0, $context->get('incrementor'));
    }
}

final class RunnerStub implements Runner
{
    private int $incrementor = 0;

    public function run(Context $context): void
    {
        $context->put('incrementor', $this->incrementor++);
    }
}

final class CacheStub implements CacheInterface
{
    public array $items = [];

    public function get($key, $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null): bool
    {
        $this->items[$key] = $value;

        return true;
    }

    public function delete(string $key): bool
    {
        return true;
    }

    public function clear(): bool
    {
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return [];
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return true;
    }
}
