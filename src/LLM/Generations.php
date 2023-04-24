<?php

declare(strict_types=1);

namespace Vexo\LLM;

use Ramsey\Collection\AbstractCollection;

final class Generations extends AbstractCollection
{
    public static function fromString(string $text): Generations
    {
        return new Generations([new Generation($text)]);
    }

    public function getType(): string
    {
        return Generation::class;
    }

    public function __toString(): string
    {
        return implode("\n", $this->data);
    }
}
