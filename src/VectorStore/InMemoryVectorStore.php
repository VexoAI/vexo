<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Model\Embedding\EmbeddingModel;

final class InMemoryVectorStore implements VectorStore
{
    use AddTextsAndDocumentsBehavior;

    private readonly Vectors $hyperplanes;

    /**
     * @var array<string, array<int, array{vector: Vector, metadata: Metadata}>>
     */
    private array $hashBuckets = [];

    /**
     * @var callable
     */
    private $randomDimensionGenerator;

    public function __construct(
        private readonly EmbeddingModel $embeddingModel,
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly string $metadataContentsKey = 'contents',
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20,
        ?callable $randomDimensionGenerator = null
    ) {
        $this->randomDimensionGenerator = $randomDimensionGenerator ?? fn (): float => random_int(-100, 100) / 100;
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
        $documents = new Documents();
        while ( ! $priorityQueue->isEmpty()) {
            /** @var Document $document */
            $document = $priorityQueue->extract();
            $documents->add($document);
        }

        return $documents;
    }

    private function generateHyperplanes(): Vectors
    {
        $hyperplanes = new Vectors();
        for ($i = 0; $i < $this->numHyperplanes; $i++) {
            $hyperplane = [];
            for ($j = 0; $j < $this->numDimensions; $j++) {
                $hyperplane[] = ($this->randomDimensionGenerator)();
            }
            $hyperplanes[] = new Vector($hyperplane);
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
}
