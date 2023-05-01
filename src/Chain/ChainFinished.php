<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Event\BaseEvent;

final class ChainFinished extends BaseEvent
{
    public function __construct(
        public Input $input,
        public Output $output
    ) {
    }
}
