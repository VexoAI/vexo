<?php

declare(strict_types=1);

namespace Vexo\Embedding;

interface EmbeddingsModel
{
    public function embedQuery(string $query): Embedding;

    /**
     * @param array<string> $documents
     */
    public function embedDocuments(array $documents): Embeddings;
}
