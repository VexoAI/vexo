<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;

interface Agent
{
    public function plan(Context $context, Steps $intermediateSteps): Step;
}
