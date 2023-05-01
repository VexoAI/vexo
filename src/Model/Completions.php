<?php

declare(strict_types=1);

namespace Vexo\Model;

use Ramsey\Collection\AbstractCollection;

final class Completions extends AbstractCollection implements \Stringable
{
    public static function fromString(string $text): self
    {
        return new self([new Completion($text)]);
    }

    public function getType(): string
    {
        return Completion::class;
    }

    public function __toString(): string
    {
        return implode("\n", $this->data);
    }
}
