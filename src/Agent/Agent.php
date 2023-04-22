<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\Chain\Input;

interface Agent
{
    public function plan(Input $input): Action|Finish;
}
