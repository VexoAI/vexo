<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Vexo\Contract\Event\BaseEvent;

final class ToolStarted extends BaseEvent
{
    public function __construct(
        public string $input
    ) {
    }
}