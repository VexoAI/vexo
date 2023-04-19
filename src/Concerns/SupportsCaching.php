<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use Psr\SimpleCache\CacheInterface;

trait SupportsCaching
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var string
     */
    private ?string $keyPrefix = null;

    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache, ?string $keyPrefix = null): void
    {
        $this->cache = $cache;
        $this->keyPrefix = $keyPrefix;

        if ($this->keyPrefix === null) {
            $this->keyPrefix = strtolower(str_replace('\\', '.', get_class($this))) . ':';
        }
    }

    /**
     * @param string $identifier The unique identifier for the cached value
     * @param callable $callback The callback to generate the value if it is not cached
     *
     * @return mixed The cached value
     */
    public function cached(string $identifier, callable $callback): mixed
    {
        if ( ! isset($this->cache)) {
            return $callback();
        }

        $key = $this->generateKey($identifier);
        $result = $this->cache->get($key);
        if ($result === null) {
            $result = $callback();
            $this->cache->set($key, $result);
        }

        return $result;
    }

    private function generateKey(string $identifier): string
    {
        return $this->keyPrefix . hash('sha256', $identifier);
    }
}
