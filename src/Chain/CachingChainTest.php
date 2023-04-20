<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

#[CoversClass(CachingChain::class)]
final class CachingChainTest extends TestCase
{
    public function testProcess(): void
    {
        $cachingChain = new CachingChain(new IncrementorChainStub(), new CacheStub());

        $firstOutput = $cachingChain->process(new Input([]));
        $secondOutput = $cachingChain->process(new Input([]));

        $this->assertEquals(1, $firstOutput->data()['incrementor']);
        $this->assertEquals(1, $secondOutput->data()['incrementor']);
    }
}

final class IncrementorChainStub implements Chain
{
    private int $incrementor = 0;

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
