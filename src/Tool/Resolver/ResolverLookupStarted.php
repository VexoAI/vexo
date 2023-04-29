<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Event\SomethingHappened;

final class ResolverLookupStarted extends SomethingHappened
{
    public function __construct(
        public string $query,
        public string $input
    ) {
    }
}
