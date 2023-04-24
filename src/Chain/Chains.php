<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Ramsey\Collection\AbstractCollection;

final class Chains extends AbstractCollection
{
    public function getType(): string
    {
        return Chain::class;
    }
}
