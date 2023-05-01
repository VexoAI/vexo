<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Completion>
 */
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