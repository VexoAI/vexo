<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Tool\Tool;
use Vexo\Tool\Tools;

abstract class BaseResolver implements Resolver, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(protected Tools $tools)
    {
    }

    public function resolve(string $query, string $input): Tool
    {
        $this->emit(new ResolverLookupStarted($query, $input));

        try {
            $tool = $this->lookup($query, $input);
        } catch (\Throwable $e) {
            $this->emit(new ResolverLookupFailed($query, $input, $e));
            throw new SorryFailedToResolveTool(sprintf('Failed to resolve tool %s: %s', $query, $e->getMessage()), $e->getCode(), $e);
        }

        $this->emit(new ResolverLookupFinished($query, $input, $tool));

        return $tool;
    }

    abstract protected function lookup(string $query, string $input): Tool;
}
