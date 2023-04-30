<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Psr\SimpleCache\CacheInterface;

final class CachingChain extends BaseChain
{
    public function __construct(
        private Chain $chain,
        private CacheInterface $cache,
        private ?int $defaultTtl = null
    ) {
    }

    public function inputKeys(): array
    {
        return $this->chain->inputKeys();
    }

    public function outputKeys(): array
    {
        return $this->chain->outputKeys();
    }

    protected function call(Input $input): Output
    {
        $cacheKey = $this->createCacheKey($input);

        $output = $this->cache->get($cacheKey);
        if ($output !== null) {
            return $output;
        }

        $output = $this->chain->process($input);
        $this->cache->set($cacheKey, $output, $this->defaultTtl);

        return $output;
    }

    private function createCacheKey(Input $input): string
    {
        return sprintf(
            '%s.%s',
            strtolower(str_replace('\\', '.', $this->chain::class)),
            hash('sha256', json_encode($input->toArray()))
        );
    }
}
