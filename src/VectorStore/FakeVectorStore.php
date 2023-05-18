<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;

final class FakeVectorStore implements VectorStore
{
    /**
     * @var array <int, Vector>
     */
    private array $vectors;

    /**
     * @var array<int, Metadata>
     */
    private array $metadatas;

    public function add(Vector $vector, Metadata $metadata): void
    {
        $this->vectors[] = $vector;
        $this->metadatas[] = $metadata;
    }

    public function search(string $query, int $maxResults = 4): Results
    {
        $results = new Results();

        for ($i = 0; $i < $maxResults && \array_key_exists($i, $this->vectors); $i++) {
            $results->add(new Result($this->vectors[$i], $this->metadatas[$i], 0.5));
        }

        return $results;
    }
}
