<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use Psr\SimpleCache\CacheInterface;
use Vexo\Weave\Concerns\SupportsCaching\NoCache;

trait SupportsCaching
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function cache(): CacheInterface
    {
        if ( ! isset($this->cache)) {
            $this->cache = new NoCache();
        }

        return $this->cache;
    }
}
