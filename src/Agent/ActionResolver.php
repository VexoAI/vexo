<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use Vexo\Weave\Chain\Output;

interface ActionResolver
{
    public function formatInstructions(): string;

    public function parse(Output $output): Action|Finish;
}
