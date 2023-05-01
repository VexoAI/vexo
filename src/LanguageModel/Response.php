<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

final class Response
{
    public static function fromString(string $response): self
    {
        return new self(Completions::fromString($response));
    }

    public function __construct(
        private readonly Completions $completions,
        private readonly ResponseMetadata $metadata = new ResponseMetadata(),
    ) {
    }

    public function completions(): Completions
    {
        return $this->completions;
    }

    public function metadata(): ResponseMetadata
    {
        return $this->metadata;
    }
}
