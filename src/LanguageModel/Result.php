<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

final class Result
{
    /**
     * @param array<string> $completions
     */
    public function __construct(
        private readonly array $completions,
        private readonly MetadataContract $metadata = new Metadata(),
    ) {
    }

    public function completions(): array
    {
        return $this->completions;
    }

    public function metadata(): MetadataContract
    {
        return $this->metadata;
    }
}
