<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;

interface AgentOutputParser extends OutputParser
{
    public function parse(string $text): Action|Finish;
}
