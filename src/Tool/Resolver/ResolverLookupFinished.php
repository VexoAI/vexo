<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Event\BaseEvent;
use Vexo\Tool\Tool;

final class ResolverLookupFinished extends BaseEvent
{
    public function __construct(
        public string $query,
        public string $input,
        public Tool $tool
    ) {
    }
}
