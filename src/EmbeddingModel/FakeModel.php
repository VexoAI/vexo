<?php

declare(strict_types=1);

namespace Vexo\EmbeddingModel;

use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;

final class FakeModel implements EmbeddingModel
{
    /**
     * @var VectorContract[]
     */
    private array $vectors = [];

    public function addVector(VectorContract $vector): void
    {
        $this->vectors[] = $vector;
    }

    public function embedQuery(string $query): VectorContract
    {
        if ($this->vectors === []) {
            throw new \LogicException('No more vectors to return.');
        }

        return array_shift($this->vectors);
    }

    public function embedTexts(array $texts): VectorsContract
    {
        if ($this->vectors === []) {
            throw new \LogicException('No more vectors to return.');
        }

        $vectors = new Vectors();
        foreach ($texts as $text) {
            $vectors->add(array_shift($this->vectors));
        }

        return $vectors;
    }
}
