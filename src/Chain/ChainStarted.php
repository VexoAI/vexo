<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Event\BaseEvent;

final class ChainStarted extends BaseEvent
{
    public function __construct(
        public Input $input
    ) {
    }
}
