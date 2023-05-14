<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use Vexo\Chain\LanguageModelChain\Blueprint;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Chain\LanguageModelChain\Prompt\TwigRenderer;

final class ReasonAndAct implements Blueprint
{
    public function promptRenderer(): Renderer
    {
        return TwigRenderer::createWithFilesystemLoader(
            'reason-and-act.twig',
            __DIR__ . '/../Prompt/templates'
        );
    }

    public function outputParser(): OutputParser
    {
        return new RegexOutputParser(
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

    public function requiredContextValues(): array
    {
        return ['tools', 'question', 'scratchpad'];
    }

    /**
     * @return array<string>
     */
    public function stops(): array
    {
        return [];
    }
}
