<?php

declare(strict_types=1);

namespace Vexo\EmbeddingModel;

use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;

interface EmbeddingModel
{
    public function embedQuery(string $query): Vector;

    /**
     * @param array<string> $texts
     */
    public function embedTexts(array $texts): Vectors;
}
