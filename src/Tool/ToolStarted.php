<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Vexo\Event\BaseEvent;

final class ToolStarted extends BaseEvent
{
    public function __construct(
        public string $input
    ) {
    }
}
