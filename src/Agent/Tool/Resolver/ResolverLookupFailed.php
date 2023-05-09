<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Contract\Event\BaseEvent;

final class ResolverLookupFailed extends BaseEvent
{
    public function __construct(
        public string $query,
        public string $input,
        public \Throwable $exception
    ) {
    }
}