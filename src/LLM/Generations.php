<?php

declare(strict_types=1);

namespace Vexo\LLM;

final class Generations implements \IteratorAggregate, \Countable, \ArrayAccess, \Stringable
{
    /**
     * @var Generation[]
     */
    private array $generations;

    public static function fromString(string $generations): Generations
    {
        return new Generations(...array_map(
            fn (string $generation) => new Generation($generation),
            explode("\n\n", $generations)
        ));
    }

    public function __construct(Generation ...$generations)
    {
        $this->generations = $generations;
    }

    public function __toString(): string
    {
        return implode("\n", $this->generations);
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->generations);
    }

    public function count(): int
    {
        return count($this->generations);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->generations[$offset]);
    }

    public function offsetGet($offset): ?Generation
    {
        return $this->generations[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->generations[] = $value;
        } else {
            $this->generations[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->generations[$offset]);
    }
}
