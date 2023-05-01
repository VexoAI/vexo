<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Psr\SimpleCache\CacheInterface;

final class CachingChain extends BaseChain
{
    public function __construct(
        private readonly Chain $chain,
        private readonly CacheInterface $cache,
        private readonly ?int $lifetime = null,
        private ?string $cacheKeyPrefix = null
    ) {
        $this->cacheKeyPrefix ??= strtolower(str_replace('\\', '.', $this->chain::class));
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
        $this->cache->set($cacheKey, $output, $this->lifetime);

        return $output;
    }

    private function createCacheKey(Input $input): string
    {
        return sprintf(
            '%s.%s',
            $this->cacheKeyPrefix,
            hash('sha256', json_encode($input->toArray(), \JSON_THROW_ON_ERROR))
        );
    }
}
