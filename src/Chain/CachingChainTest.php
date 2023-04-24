<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

#[CoversClass(CachingChain::class)]
final class CachingChainTest extends TestCase
{
    private CachingChain $cachingChain;

    public function setUp(): void
    {
        $this->cachingChain = new CachingChain(new ChainStub(), new CacheStub());
    }

    public function testProcess(): void
    {
        $firstOutput = $this->cachingChain->process(new Input([]));
        $secondOutput = $this->cachingChain->process(new Input([]));

        $this->assertEquals(1, $firstOutput->data()['incrementor']);
        $this->assertEquals(1, $secondOutput->data()['incrementor']);
    }

    public function testInputKeysDelegatesToChain(): void
    {
        $this->assertSame([], $this->cachingChain->inputKeys());
    }

    public function testOutputKeysDelegatesToChain(): void
    {
        $this->assertSame(['incrementor'], $this->cachingChain->outputKeys());
    }
}

final class ChainStub implements Chain
{
    private int $incrementor = 0;

    public function inputKeys(): array
    {
        return [];
    }

    public function outputKeys(): array
    {
        return ['incrementor'];
    }

    public function process(Input $input): Output
    {
        $this->incrementor++;

        return new Output(['incrementor' => $this->incrementor]);
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
