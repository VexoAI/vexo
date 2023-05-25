<?php

declare(strict_types=1);

namespace Vexo\Contract;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Vector>
 */
final class Vectors extends AbstractCollection
{
    public function getType(): string
    {
        return Vector::class;
    }
}
