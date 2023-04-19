<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use Psr\SimpleCache\CacheInterface;

interface CacheAware
{
    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache, ?string $keyPrefix): void;
}
