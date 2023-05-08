<?php

declare(strict_types=1);

namespace Vexo\VectorStore\InMemoryVectorStore;

use Vexo\Contract\Vector\Vector as VectorContract;

interface LocalitySensitiveHashing
{
    public function project(string $id, VectorContract $vector): void;

    /**
     * @return array<string>
     */
    public function getCandidateIdsForVector(VectorContract $vector): array;
}
