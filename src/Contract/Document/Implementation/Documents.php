<?php

declare(strict_types=1);

namespace Vexo\Contract\Document\Implementation;

use Ramsey\Collection\AbstractCollection;
use Vexo\Contract\Document\Document as DocumentContract;
use Vexo\Contract\Document\Documents as DocumentsContract;

final class Documents extends AbstractCollection implements DocumentsContract
{
    public function getType(): string
    {
        return DocumentContract::class;
    }
}
