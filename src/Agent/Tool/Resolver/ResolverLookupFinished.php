<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Agent\Tool\Tool;
use Vexo\Contract\Event\BaseEvent;

final class ResolverLookupFinished extends BaseEvent
{
    public function __construct(
        public string $query,
        public string $input,
        public Tool $tool
    ) {
    }
}
