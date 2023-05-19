<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata as MetadataContract;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;
use Vexo\Model\Embedding\EmbeddingModel;

/**
 * @phpstan-type HashBuckets array<string, array<int, array{vector: Vector, metadata: MetadataContract}>>
 */
final class InMemoryVectorStore implements VectorStore
{
    private readonly Vectors $hyperplanes;

    /**
     * @var HashBuckets
     */
    private array $hashBuckets = [];

    /**
     * @var callable
     */
    private $randomDimensionGenerator;

    public function __construct(
        private readonly EmbeddingModel $embeddingModel,
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20,
        ?callable $randomDimensionGenerator = null
    ) {
        $this->randomDimensionGenerator = $randomDimensionGenerator ?? fn (): float => random_int(-100, 100) / 100;
        $this->hyperplanes = $this->generateHyperplanes();
    }

    public function add(Vector $vector, MetadataContract $metadata): void
    {
        $this->hashBuckets[$this->generateHash($vector)][] = [
            'vector' => $vector,
            'metadata' => $metadata
        ];
    }

    public function search(string $query, int $maxResults = 4): Results
    {
        // Embed the query
        $queryVector = $this->embeddingModel->embedQuery($query);

        // Retrieve the vectors that have the same LSH hash as the query
        $candidates = $this->hashBuckets[$this->generateHash($queryVector)] ?? [];

        // Initialize priority queue to keep track of the highest scoring vectors
        /** @var \SplPriorityQueue<float, Result> */
        $priorityQueue = new \SplPriorityQueue();

        // Iterate over the candidate vectors and calculate the similarity between the query and the candidate vectors
        foreach ($candidates as $candidate) {
            $score = $queryVector->similarity($candidate['vector'], $this->similarityAlgorithm);

            // Insert the search result into the priority queue
            $priorityQueue->insert(
                new Result($candidate['vector'], $candidate['metadata'], $score),
                $score
            );

            // Remove the lowest scoring vector from the priority queue if it exceeds the maximum number of results
            if ($priorityQueue->count() > $maxResults) {
                $priorityQueue->extract();
            }
        }

        // Generate the search results.
        $results = new Results();
        while ( ! $priorityQueue->isEmpty()) {
            /** @var Result $result */
            $result = $priorityQueue->extract();
            $results->add($result);
        }

        return $results;
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
