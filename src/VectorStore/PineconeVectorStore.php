<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Probots\Pinecone\Resources\VectorResource;
use Vexo\Contract\Metadata;
use Vexo\Contract\Vectors;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Model\Embedding\Model;
use Webmozart\Assert\Assert;

final class PineconeVectorStore implements VectorStore
{
    use AddTextsAndDocumentsBehavior;

    /**
     * @param int<1, max> $upsertBatchSize
     */
    public function __construct(
        private readonly Model $embeddingModel,
        private readonly VectorResource $pinecone,
        private readonly string $metadataIdKey = 'id',
        private readonly string $metadataContentsKey = 'contents',
        private readonly ?string $namespace = null,
        private readonly int $upsertBatchSize = 100,
    ) {
    }

    /**
     * @param array<int, Metadata> $metadatas
     */
    public function addVectors(Vectors $vectors, array $metadatas): void
    {
        $vectorChunks = array_chunk($vectors->toArray(), $this->upsertBatchSize, true);
        $metadataChunks = array_chunk($metadatas, $this->upsertBatchSize, true);

        foreach ($vectorChunks as $chunkIndex => $vectorChunk) {
            $data = [];
            foreach ($vectorChunk as $index => $vector) {
                $data[] = [
                    'id' => $metadataChunks[$chunkIndex][$index]->get($this->metadataIdKey) ?? $this->generateUuid(),
                    'values' => $vector->toArray(),
                    'metadata' => $metadataChunks[$chunkIndex][$index]->toArray(),
                ];
            }

            try {
                $response = $this->pinecone->upsert(
                    namespace: $this->namespace,
                    vectors: $data
                );

                Assert::true($response->successful(), 'Request was not successful: ' . $response->body());
            } catch (\Throwable $exception) {
                throw FailedToAddVectors::because($exception);
            }
        }
    }

    public function similaritySearch(
        string $query,
        int $maxResults = 4,
        bool $includeScoresInMetadata = true
    ): Documents {
        try {
            $vector = $this->embeddingModel->embedQuery($query);

            $response = $this->pinecone->query(
                namespace: $this->namespace,
                vector: $vector->toArray(),
                includeMetadata: true,
                topK: $maxResults
            );

            Assert::true($response->successful(), 'Request was not successful: ' . $response->body());
        } catch (\Throwable $exception) {
            throw FailedToPerformSimilaritySearch::because($exception);
        }

        /** @var array<int, array{id: string, score: float, metadata: array<string, mixed>}> $matches */
        $matches = $response->json('matches');
        $documents = new Documents();
        foreach ($matches as $match) {
            $metadata = new Metadata($match['metadata']);
            $metadata->put('score', $match['score']);
            $metadata->putIfAbsent($this->metadataIdKey, $match['id']);
            /** @var string $contents */
            $contents = $metadata->get($this->metadataContentsKey, '');
            $documents->add(new Document($contents, $metadata));
        }

        return $documents;
    }

    private function generateUuid(): string
    {
        $bytes = random_bytes(16);

        $bytes[6] = \chr(\ord($bytes[6]) & 0x0F | 0x40); // set version to 0100
        $bytes[8] = \chr(\ord($bytes[8]) & 0x3F | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
