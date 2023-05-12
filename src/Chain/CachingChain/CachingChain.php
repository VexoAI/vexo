<?php

declare(strict_types=1);

namespace Vexo\Chain\CachingChain;

use Psr\SimpleCache\CacheInterface;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\Runner;

final class CachingChain implements Chain
{
    public function __construct(
        private readonly Runner $runner,
        private readonly CacheInterface $cache,
        private readonly array $contextInputValuesToMatch,
        private readonly array $contextOutputValuesToCache,
        private readonly ?int $lifetime = null,
        private readonly string $cacheKeyPrefix = 'vexo.chain.cache'
    ) {
    }

    public function run(Context $context): void
    {
        $cacheKey = $this->createCacheKey($context);

        $cachedContextValues = $this->cache->get($cacheKey);
        if ($cachedContextValues !== null) {
            $this->putValuesIntoContext($context, $cachedContextValues);

            return;
        }

        $this->runner->run($context);
        $this->cache->set(
            $cacheKey,
            $this->extractValuesFromContext($context, $this->contextOutputValuesToCache),
            $this->lifetime
        );
    }

    private function createCacheKey(Context $context): string
    {
        return sprintf(
            '%s.%s',
            $this->cacheKeyPrefix,
            hash('sha256', serialize(
                $this->extractValuesFromContext($context, $this->contextInputValuesToMatch)
            ))
        );
    }

    private function extractValuesFromContext(Context $context, array $contextValues): array
    {
        $values = [];
        foreach ($contextValues as $contextValue) {
            $values[$contextValue] = $context->get($contextValue);
        }

        return $values;
    }

    private function putValuesIntoContext(Context $context, array $values): void
    {
        foreach ($values as $key => $value) {
            $context->put($key, $value);
        }
    }
}
