<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\SomethingHappened;

final class ChainStarted extends SomethingHappened
{
    public function __construct(
        public Input $input
    ) {
    }
}
