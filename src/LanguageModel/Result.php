<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

final class Result
{
    /**
     * @param array<string> $generations
     */
    public function __construct(
        private readonly array $generations,
        private readonly MetadataContract $metadata = new Metadata(),
    ) {
    }

    /**
     * @return array<string>
     */
    public function generations(): array
    {
        return $this->generations;
    }

    public function metadata(): MetadataContract
    {
        return $this->metadata;
    }
}
