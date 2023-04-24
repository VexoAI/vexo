<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Input;

interface Agent
{
    /**
     * @param Input $input
     * @param Step[] $intermediateSteps
     */
    public function plan(Input $input, array $intermediateSteps): Step;
}
