<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

use Vexo\Contract\Metadata\Metadata;

final class Result
{
    /**
     * @param array<string> $generations
     */
    public function __construct(
        private readonly array $generations,
        private readonly Metadata $metadata = new Metadata(),
    ) {
    }

    /**
     * @return array<string>
     */
    public function generations(): array
    {
        return $this->generations;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }
}
