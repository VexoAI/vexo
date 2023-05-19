<?php

declare(strict_types=1);

namespace Vexo\Document\Repository;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Model\Embedding\EmbeddingModel;
use Vexo\VectorStore\VectorStore;

final class VectorStoreRepository implements Repository
{
    private const METADATA_CONTENTS = '__document_contents';

    /**
     * @param array<string> $metadataToEmbed
     */
    public function __construct(
        private readonly EmbeddingModel $embeddingModel,
        private readonly VectorStore $vectorStore,
        private readonly array $metadataToEmbed = []
    ) {
    }

    public function persist(Document $document): void
    {
        $this->vectorStore->add(
            $this->createVector($document),
            $this->createMetadata($document)
        );
    }

    public function search(string $query, int $maxResults = 4): Documents
    {
        $results = $this->vectorStore->search($query, $maxResults);

        $documents = new Documents();
        foreach ($results as $result) {
            $metadata = clone $result->metadata();

            /** @var string $contents */
            $contents = $metadata->remove(self::METADATA_CONTENTS);

            $documents->add(new Document($contents, $metadata));
        }

        return $documents;
    }

    private function createVector(Document $document): Vector
    {
        $metadataTextToEmbed = implode(
            ' ',
            array_intersect_key(
                $document->metadata()->toArray(),
                array_flip($this->metadataToEmbed)
            )
        );

        return $this->embeddingModel->embedTexts(
            [$metadataTextToEmbed . ' ' . $document->contents()]
        )->first();
    }

    private function createMetadata(Document $document): Metadata
    {
        $metadata = clone $document->metadata();
        $metadata->put(self::METADATA_CONTENTS, $document->contents());

        return $metadata;
    }
}
