<?php

declare(strict_types=1);

namespace Vexo\Chain\CachingChain;

use Psr\SimpleCache\CacheInterface;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;

final class CachingChain implements Chain
{
    /**
     * @param array<string> $inputValuesToMatch
     * @param array<string> $outputValuesToCache
     */
    public function __construct(
        private readonly Chain $chain,
        private readonly CacheInterface $cache,
        private readonly array $inputValuesToMatch,
        private readonly array $outputValuesToCache,
        private readonly ?int $lifetime = null,
        private readonly string $cacheKeyPrefix = 'vexo.chain.cache'
    ) {
    }

    public function run(Context $context): void
    {
        $cacheKey = $this->createCacheKey($context);

        /** @var array<string, mixed>|null $cachedContextValues */
        $cachedContextValues = $this->cache->get($cacheKey);
        if ($cachedContextValues !== null) {
            $this->putValuesIntoContext($context, $cachedContextValues);

            return;
        }

        $this->chain->run($context);
        $this->cache->set(
            $cacheKey,
            $this->extractValuesFromContext($context, $this->outputValuesToCache),
            $this->lifetime
        );
    }

    private function createCacheKey(Context $context): string
    {
        return sprintf(
            '%s.%s',
            $this->cacheKeyPrefix,
            hash('sha256', serialize(
                $this->extractValuesFromContext($context, $this->inputValuesToMatch)
            ))
        );
    }

    /**
     * @param array<string> $contextValues
     *
     * @return array<string, mixed>
     */
    private function extractValuesFromContext(Context $context, array $contextValues): array
    {
        $values = [];
        foreach ($contextValues as $contextValue) {
            $values[$contextValue] = $context->get($contextValue);
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $values
     */
    private function putValuesIntoContext(Context $context, array $values): void
    {
        foreach ($values as $key => $value) {
            $context->put($key, $value);
        }
    }
}
