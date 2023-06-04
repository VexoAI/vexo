<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata;
use Vexo\Contract\SimilarityAlgorithm;
use Vexo\Contract\Vector;
use Vexo\Contract\Vectors;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Model\Embedding\Model;

final class InMemoryVectorStore implements VectorStore
{
    use AddTextsAndDocumentsBehavior;

    private readonly Vectors $hyperplanes;

    /**
     * @var array<string, array<int, array{vector: Vector, metadata: Metadata}>>
     */
    private array $hashBuckets = [];

    public function __construct(
        private readonly Model $embeddingModel,
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly string $metadataContentsKey = 'contents',
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20
    ) {
        $this->hyperplanes = $this->generateHyperplanes();
    }

    /**
     * @param array<Metadata> $metadatas
     */
    public function addVectors(Vectors $vectors, array $metadatas): void
    {
        foreach ($vectors as $index => $vector) {
            $this->hashBuckets[$this->generateHash($vector)][] = [
                'vector' => $vector,
                'metadata' => clone $metadatas[$index]
            ];
        }
    }

    public function similaritySearch(
        string $query,
        int $maxResults = 4,
        bool $includeScoresInMetadata = true
    ): Documents {
        // Embed the query
        $queryVector = $this->embeddingModel->embedQuery($query);

        // Retrieve the vectors that have the same LSH hash as the query
        $candidates = $this->hashBuckets[$this->generateHash($queryVector)] ?? [];

        // Initialize priority queue to keep track of the highest scoring vectors
        /** @var \SplPriorityQueue<float, Document> */
        $priorityQueue = new \SplPriorityQueue();

        // Iterate over the candidate vectors and calculate the similarity between the query and the candidate vectors
        foreach ($candidates as $candidate) {
            $score = $queryVector->similarity($candidate['vector'], $this->similarityAlgorithm);
            /** @var string $contents */
            $contents = $candidate['metadata']->get($this->metadataContentsKey, '');
            $metadata = clone $candidate['metadata'];

            if ($includeScoresInMetadata) {
                $metadata->put('score', $score);
            }

            $priorityQueue->insert(
                new Document($contents, $metadata),
                -$score // Negate score to maintain a max heap
            );

            // Remove the lowest scoring vector from the priority queue if it exceeds the maximum number of results
            if ($priorityQueue->count() > $maxResults) {
                $priorityQueue->extract();
            }
        }

        // Generate the search results.
        $documents = [];
        while ( ! $priorityQueue->isEmpty()) {
            /** @var Document $document */
            $document = $priorityQueue->extract();
            array_unshift($documents, $document);
        }

        return new Documents($documents);
    }

    private function generateHyperplanes(): Vectors
    {
        $hyperplanes = new Vectors();
        for ($i = 0; $i < $this->numHyperplanes; $i++) {
            $hyperplanes->add($this->generateVector($this->numDimensions));
        }

        return $hyperplanes;
    }

    private function generateHash(Vector $vector): string
    {
        $hash = '';
        foreach ($this->hyperplanes as $hyperplane) {
            $hash .= $vector->similarity($hyperplane, $this->similarityAlgorithm) >= 0 ? '1' : '0';
        }

        return $hash;
    }

    private function generateVector(int $numDimensions): Vector
    {
        return new Vector(
            array_map(
                fn (): float => $this->generateFloat(),
                range(0, $numDimensions - 1)
            )
        );
    }

    private function generateFloat(): float
    {
        return -1 + 2 * (random_int(0, mt_getrandmax()) / mt_getrandmax());
    }
}
