<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vectors;
use Vexo\Document\Documents;

trait AddTextsAndDocumentsBehavior
{
    public function addDocuments(Documents $documents): void
    {
        $texts = [];
        $metadatas = [];

        foreach ($documents as $document) {
            $texts[] = $document->contents();
            $metadatas[] = $document->metadata();
        }

        $this->addTexts($texts, $metadatas);
    }

    /**
     * @param array<int, string> $texts
     * @param array<int, Metadata> $metadatas
     */
    public function addTexts(array $texts, array $metadatas): void
    {
        $this->addVectors(
            $this->embeddingModel->embedTexts($texts),
            array_map(
                function (int $index, Metadata $originalMetadata) use ($texts): Metadata {
                    $metadata = clone $originalMetadata;
                    $metadata->put($this->metadataContentsKey, $texts[$index]);

                    return $metadata;
                },
                array_keys($metadatas),
                array_values($metadatas)
            )
        );
    }

    /**
     * @param array<Metadata> $metadatas
     */
    abstract public function addVectors(Vectors $vectors, array $metadatas): void;
}
