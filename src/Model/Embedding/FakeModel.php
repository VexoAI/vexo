<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;

final class FakeModel implements EmbeddingModel
{
    /**
     * @param array<int, Vector> $vectors
     */
    public function __construct(
        private array $vectors = []
    ) {
    }

    public function addVector(Vector $vector): void
    {
        $this->vectors[] = $vector;
    }

    public function embedTexts(array $texts): Vectors
    {
        return new Vectors(
            array_map(
                fn (string $text): Vector => $this->shiftVector(),
                $texts
            )
        );
    }

    public function embedQuery(string $query): Vector
    {
        return $this->shiftVector();
    }

    private function shiftVector(): Vector
    {
        if ($this->vectors === []) {
            throw new \LogicException('No vectors left to return');
        }

        return array_shift($this->vectors);
    }
}
