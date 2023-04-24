<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;

interface Agent
{
    public function plan(Input $input, Steps $intermediateSteps): Step;
}
