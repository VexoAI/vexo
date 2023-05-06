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

final class InMemoryVectorStore implements VectorStoreWriter, VectorStoreReader
{
    public function __construct(
        private readonly VectorsContract $vectors = new Vectors(),
        private readonly MetadatasContract $metadatas = new Metadatas(),
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE
    ) {
    }

    public function add(string $id, VectorContract $vector, MetadataContract $metadata): void
    {
        $this->vectors->offsetSet($id, $vector);
        $this->metadatas->offsetSet($id, $metadata);
    }

    public function search(VectorContract $query, int $numResults = 1): SearchResults
    {
        // Perform a brute-force search by calculating the similarity between the query and all vectors in the store.
        $similarityScores = [];
        foreach ($this->vectors as $id => $vector) {
            $similarityScores[$id] = $query->similarity($vector, $this->similarityAlgorithm);
        }
        arsort($similarityScores, \SORT_NUMERIC);

        // Cut off all but the top N results.
        $similarityScores = \array_slice($similarityScores, 0, $numResults, true);

        // Generate the search results.
        $results = new SearchResults();
        foreach ($similarityScores as $id => $score) {
            $results->add(new SearchResult($id, $score, $this->metadatas->offsetGet($id)));
        }

        return $results;
    }
}
