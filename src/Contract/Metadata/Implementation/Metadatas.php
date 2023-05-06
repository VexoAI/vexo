<?php

declare(strict_types=1);

namespace Vexo\Contract\Metadata\Implementation;

use Ramsey\Collection\AbstractCollection;
use Vexo\Contract\Metadata\Metadata as MetadataContract;
use Vexo\Contract\Metadata\Metadatas as MetadatasContract;

final class Metadatas extends AbstractCollection implements MetadatasContract
{
    public function getType(): string
    {
        return MetadataContract::class;
    }
}
