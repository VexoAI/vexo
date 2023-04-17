<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Assert\Assertion as Ensure;

final class Response
{
    /**
     * @param Generation[] $generations
     */
    public function __construct(private array $generations)
    {
        Ensure::allIsInstanceOf($generations, Generation::class);
    }

    /**
     * @return Generation[]
     */
    public function generations(): array
    {
        return $this->generations;
    }
}