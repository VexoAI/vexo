<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

final class SupportsCachingTest extends TestCase
{
    public function testCached(): void
    {
        $cache = new CacheStub();
        $supportsCache = new SupportsCachingSUT();
        $supportsCache->setCache($cache);

        $firstValue = $supportsCache->cached('my-identifier', function () {
            return 'The first result, should be cached';
        });
        $this->assertEquals('The first result, should be cached', $firstValue);

        $secondValue = $supportsCache->cached('my-identifier', function () {
            return 'The second result';
        });
        $this->assertEquals('The first result, should be cached', $secondValue);
    }

    public function testCustomPrefix(): void
    {
        $cache = new CacheStub();
        $supportsCache = new SupportsCachingSUT();
        $supportsCache->setCache($cache, 'my-prefix:');

        $supportsCache->cached('my-identifier', function () {
            return 'The result';
        });

        $this->assertArrayHasKey(
            'my-prefix:bd7f41d131ab8224870c9f5e3916665d6ccd8bc9241b21c357ae2cd0c9f40aa7',
            $cache->items
        );
    }

    public function testWithoutCache(): void
    {
        $supportsCache = new SupportsCachingSUT();

        $value = $supportsCache->cached('key', function () {
            return 'Some result';
        });

        $this->assertEquals('Some result', $value);
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
