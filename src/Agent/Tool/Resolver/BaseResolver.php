<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Agent\Tool\Tool;
use Vexo\Agent\Tool\Tools;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;

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
