<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Implementation\Metadatas;
use Vexo\Contract\Metadata\Metadata as MetadataContract;
use Vexo\Contract\Metadata\Metadatas as MetadatasContract;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;

/**
 * In-memory vector store that uses locality-sensitive hashing (LSH) to speed up similarity searches.
 */
final class InMemoryVectorStore implements VectorStoreWriter, VectorStoreReader
{
    private VectorsContract $hyperplanes;

    private array $lshHashBuckets = [];

    public function __construct(
        private readonly VectorsContract $vectors = new Vectors(),
        private readonly MetadatasContract $metadatas = new Metadatas(),
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20
    ) {
        $this->generateHyperplanes();
    }

    public function add(string $id, VectorContract $vector, MetadataContract $metadata): void
    {
        $this->vectors->offsetSet($id, $vector);
        $this->metadatas->offsetSet($id, $metadata);
        $this->lshHashBuckets[$this->hashVector($vector)][] = $id;
    }

    public function search(VectorContract $query, int $numResults = 1): SearchResults
    {
        // Retrieve the vector IDs that have the same LSH hash
        $candidateIds = $this->lshHashBuckets[$this->hashVector($query)] ?? [];

        // Initialize priority queue to keep track of the highest scoring vectors
        $priorityQueue = new \SplPriorityQueue();

        // Iterate over the candidate vectors and calculate the similarity between the query and the candidate vectors
        foreach ($candidateIds as $id) {
            $score = $query->similarity($this->vectors[$id], $this->similarityAlgorithm);

            // Insert the search result into the priority queue
            $priorityQueue->insert(
                new SearchResult($id, $score, $this->metadatas[$id]),
                -$score // Use negative score as priority to maintain a max-heap
            );

            // Remove the lowest scoring vector from the priority queue if it exceeds the maximum number of results
            if ($priorityQueue->count() > $numResults) {
                $priorityQueue->extract();
            }
        }

        // Generate the search results.
        $results = [];
        while ( ! $priorityQueue->isEmpty()) {
            array_unshift($results, $priorityQueue->extract());
        }

        return new SearchResults($results);
    }

    private function generateHyperplanes(): void
    {
        $this->hyperplanes = new Vectors();
        for ($i = 0; $i < $this->numHyperplanes; $i++) {
            $hyperplane = [];
            for ($j = 0; $j < $this->numDimensions; $j++) {
                $hyperplane[] = random_int(-100, 100) / 100;
            }
            $this->hyperplanes[] = new Vector($hyperplane);
        }
    }

    private function hashVector(VectorContract $vector): string
    {
        $hash = '';
        foreach ($this->hyperplanes as $hyperplane) {
            $hash .= $vector->similarity($hyperplane, $this->similarityAlgorithm) >= 0 ? '1' : '0';
        }

        return $hash;
    }
}
