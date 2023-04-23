<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\Chain\Input;

interface Agent
{
    /**
     * @param Input $input
     * @param Step[] $intermediateSteps
     */
    public function plan(Input $input, array $intermediateSteps): Step;
}
