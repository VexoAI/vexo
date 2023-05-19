<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;

final class FakeModel implements EmbeddingModel
{
    /**
     * @var Vector[]
     */
    private array $vectors = [];

    public function addVector(Vector $vector): void
    {
        $this->vectors[] = $vector;
    }

    public function embedQuery(string $query): Vector
    {
        if ($this->vectors === []) {
            throw new \LogicException('No more vectors to return.');
        }

        return array_shift($this->vectors);
    }

    public function embedTexts(array $texts): Vectors
    {
        if ($this->vectors === []) {
            throw new \LogicException('No more vectors to return.');
        }

        $vectors = new Vectors();
        foreach ($texts as $text) {
            /** @var Vector $vector */
            $vector = array_shift($this->vectors);
            $vectors->add($vector);
        }

        return $vectors;
    }
}
