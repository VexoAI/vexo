<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Event\SomethingHappened;

final class ChainFinished extends SomethingHappened
{
    public function __construct(
        public Input $input,
        public Output $output
    ) {
    }
}
