<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector\Implementation;

use Ramsey\Collection\AbstractCollection;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;

final class Vectors extends AbstractCollection implements VectorsContract
{
    public function getType(): string
    {
        return VectorContract::class;
    }
}
