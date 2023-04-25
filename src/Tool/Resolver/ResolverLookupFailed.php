<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\SomethingHappened;

final class ResolverLookupFailed extends SomethingHappened
{
    public function __construct(
        public string $query,
        public string $input,
        public \Throwable $exception
    ) {
    }
}