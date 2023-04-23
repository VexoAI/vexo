<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Vexo\Weave\SomethingHappened;

final class ChainFinished extends SomethingHappened
{
    public function __construct(
        public Input $input,
        public Output $output
    ) {
    }
}
