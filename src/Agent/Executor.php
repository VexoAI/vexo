<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;

interface Executor extends Chain
{
    public function run(Context $context): void;
}
