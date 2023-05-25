<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata;
use Vexo\Contract\Vectors;
use Vexo\Document\Documents;

interface VectorStore
{
    public function addDocuments(Documents $documents): void;

    /**
     * @param array<int, string>   $texts
     * @param array<int, Metadata> $metadatas
     */
    public function addTexts(array $texts, array $metadatas): void;

    /**
     * @param array<int, Metadata> $metadatas
     */
    public function addVectors(Vectors $vectors, array $metadatas): void;

    public function similaritySearch(
        string $query,
        int $maxResults = 4,
        bool $includeScoresInMetadata = true
    ): Documents;
}
