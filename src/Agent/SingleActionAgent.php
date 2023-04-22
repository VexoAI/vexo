<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\Chain\Chain;
use Vexo\Weave\Chain\Input;

final class SingleActionAgent implements Agent
{
    public function __construct(
        private Chain $llmChain,
        private ActionResolver $actionResolver
    ) {
    }

    public function plan(Input $input): Action|Finish
    {
        return $this->actionResolver->parse(
            $this->llmChain->process($input)
        );
    }
}
