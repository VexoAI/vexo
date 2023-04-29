<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Event\SomethingHappened;
use Vexo\Tool\Tool;

final class ResolverLookupFinished extends SomethingHappened
{
    public function __construct(
        public string $query,
        public string $input,
        public Tool $tool
    ) {
    }
}
