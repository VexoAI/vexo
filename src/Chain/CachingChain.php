<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Psr\Log\LoggerAwareInterface;
use Psr\SimpleCache\CacheInterface;
use Vexo\Weave\Chain\Validation\SupportsInputValidation;
use Vexo\Weave\Logging\SupportsLogging;

final class CachingChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;
    use SupportsInputValidation;

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

    public function process(Input $input): Output
    {
        $this->validateInput($input);

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
