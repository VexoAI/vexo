<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Event\BaseEvent;

final class ResolverLookupStarted extends BaseEvent
{
    public function __construct(
        public string $query,
        public string $input
    ) {
    }
}
