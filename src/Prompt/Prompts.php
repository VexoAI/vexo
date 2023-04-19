<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use Assert\Assertion as Ensure;

final class Prompts implements \IteratorAggregate, \Countable
{
    /**
     * @var Prompt[]
     */
    private array $prompts;

    public function __construct(Prompt ...$prompts)
    {
        Ensure::minCount($prompts, 1, 'At least one prompt is required');
        $this->prompts = $prompts;
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->prompts);
    }

    public function count(): int
    {
        return count($this->prompts);
    }

    /**
     * @return Prompt[]
     */
    public function toArray(): array
    {
        return $this->prompts;
    }
}
