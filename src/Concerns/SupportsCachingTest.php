<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Vexo\Weave\Concerns\SupportsCaching\NoCache;

#[CoversClass(SupportsCaching::class)]
final class SupportsCachingTest extends TestCase
{
    public function testCacheSetGet(): void
    {
        $cache = new CacheStub();
        $supportsCache = new SupportsCachingSUT();
        $supportsCache->setCache($cache);

        $this->assertSame($cache, $supportsCache->cache());
    }

    public function testGetSetsNoCache(): void
    {
        $supportsCache = new SupportsCachingSUT();

        $this->assertInstanceOf(NoCache::class, $supportsCache->cache());
    }
}

final class SupportsCachingSUT
{
    use SupportsCaching;
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
