<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Psr\Log\LoggerAwareInterface;
use Psr\SimpleCache\CacheInterface;
use Vexo\Weave\Concerns\SupportsLogging;

final class CachingChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;

    public function __construct(
        private Chain $chain,
        private CacheInterface $cache,
        private ?int $defaultTtl = null
    ) {
    }

    public function process(Input $input): Output
    {
        $cacheKey = $this->createCacheKey($input);

        $output = $this->cache->get($cacheKey);
        if ($output !== null) {
            $this->logger()->debug('Cache hit', ['cacheKey' => $cacheKey]);

            return $output;
        }

        $this->logger()->debug('Cache miss', ['cacheKey' => $cacheKey]);
        $output = $this->chain->process($input);
        $this->cache->set($cacheKey, $output, $this->defaultTtl);

        return $output;
    }

    private function createCacheKey(Input $input): string
    {
        return sprintf(
            '%s.%s',
            strtolower(str_replace('\\', '.', $this->chain::class)),
            hash('sha256', json_encode($input->data()))
        );
    }
}
