<?php

declare(strict_types=1);

namespace Vexo\LLM;

final class Response
{
    public static function fromString(string $response): self
    {
        return new self(Generations::fromString($response));
    }

    public function __construct(
        private Generations $generations,
        private ResponseMetadata $metadata = new ResponseMetadata(),
    ) {
    }

    public function generations(): Generations
    {
        return $this->generations;
    }

    public function metadata(): ResponseMetadata
    {
        return $this->metadata;
    }
}
