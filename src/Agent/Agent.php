<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\Context;

interface Agent
{
    /**
     * Determines the next step to take given the current context and previous steps.
     */
    public function planNextStep(Context $context, Steps $previousSteps): Step|Conclusion;

    /**
     * Takes the given step and returns the completed step.
     */
    public function takeStep(Context $context, Steps $previousSteps, Step $step): Step;
}
