<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Document;
use Vexo\Contract\Event\BaseEvent;
use Vexo\Contract\Vector\Vector;

final class DocumentAdded extends BaseEvent
{
    public function __construct(
        public Document $document,
        public Vector $vector
    ) {
    }
}
