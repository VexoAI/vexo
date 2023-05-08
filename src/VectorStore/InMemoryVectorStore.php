<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Implementation\Metadatas;
use Vexo\Contract\Metadata\Metadata as MetadataContract;
use Vexo\Contract\Metadata\Metadatas as MetadatasContract;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;
use Vexo\VectorStore\InMemoryVectorStore\LocalitySensitiveHashing;

final class InMemoryVectorStore implements VectorStoreWriter, VectorStoreReader
{
    private readonly VectorsContract $vectors;

    private readonly MetadatasContract $metadatas;

    public function __construct(
        private readonly LocalitySensitiveHashing $localitySensitiveHashing,
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
    ) {
        $this->vectors = new Vectors();
        $this->metadatas = new Metadatas();
    }

    public function add(string $id, VectorContract $vector, MetadataContract $metadata): void
    {
        $this->vectors->offsetSet($id, $vector);
        $this->metadatas->offsetSet($id, $metadata);
        $this->localitySensitiveHashing->project($id, $vector);
    }

    public function search(VectorContract $query, int $numResults = 1): SearchResults
    {
        // Retrieve the vector IDs that have the same LSH hash
        $candidateIds = $this->localitySensitiveHashing->getCandidateIdsForVector($query);

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
}
