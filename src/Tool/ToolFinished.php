<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Vexo\SomethingHappened;

final class ToolFinished extends SomethingHappened
{
    public function __construct(
        public string $input,
        public string $output
    ) {
    }
}
