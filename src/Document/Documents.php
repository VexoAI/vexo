<?php

declare(strict_types=1);

namespace Vexo\Document;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Document>
 */
final class Documents extends AbstractCollection
{
    public function getType(): string
    {
        return Document::class;
    }
}
