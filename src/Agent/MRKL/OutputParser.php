<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use Vexo\Agent\Action;
use Vexo\Agent\AgentOutputParser;
use Vexo\Agent\Finish;
use Vexo\OutputParser\RegexOutputParser;

final class OutputParser extends RegexOutputParser implements AgentOutputParser
{
    public function __construct()
    {
        parent::__construct(
            '/
                (?:
                    Final\ Answer:\s*       # Match "Final Answer:" followed by optional whitespace
                    (?P<final_answer>.+)    # Capture the non-empty final answer
                )
                |                           # OR
                (?:
                    Action:\s*              # Match "Action:" followed by optional whitespace
                    (?P<action>.+?)         # Capture the non-empty action
                    \n
                    Action\s*Input:\s*      # Match "Action Input:" followed by optional whitespace
                    (?P<input>.*)           # Capture the action input
                )
            /sx'                            // s and x modifiers: s makes . match newlines, x enables free-spacing mode
        );
    }

    public function formatInstructions(): string
    {
        return Prompt::FORMAT_INSTRUCTIONS;
    }

    public function parse(string $text): Action|Finish
    {
        $matches = parent::parse($text);

        if ( ! empty($matches['final_answer'])) {
            return new Finish(['result' => $matches['final_answer']]);
        }

        return new Action($matches['action'], $matches['input']);
    }
}
