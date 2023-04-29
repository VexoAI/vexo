<?php

declare(strict_types=1);

namespace Vexo\Model;

final class Completion implements \Stringable
{
    public function __construct(private string $text)
    {
    }

    public function __toString(): string
    {
        return $this->text();
    }

    public function text(): string
    {
        return $this->text;
    }
}
