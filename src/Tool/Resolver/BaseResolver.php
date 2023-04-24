<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Assert\Assertion as Ensure;
use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
use Vexo\Tool\Tool;

abstract class BaseResolver implements Resolver, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    /**
     * @param Tool[] $tools
     */
    public function __construct(protected array $tools)
    {
        Ensure::allIsInstanceOf($this->tools, Tool::class);
    }

    public function resolve(string $query, string $input): Tool
    {
        $this->eventDispatcher()->dispatch(
            (new ResolverLookupStarted($query, $input))->for($this)
        );

        try {
            $tool = $this->lookup($query, $input);
        } catch (\Throwable $e) {
            $this->eventDispatcher()->dispatch(
                (new ResolverLookupFailed($query, $input, $e))->for($this)
            );
            throw new SorryFailedToResolveTool(
                sprintf('Failed to resolve tool %s: %s', $query, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        $this->eventDispatcher()->dispatch(
            (new ResolverLookupFinished($query, $input, $tool))->for($this)
        );

        return $tool;
    }

    abstract protected function lookup(string $query, string $input): Tool;
}
