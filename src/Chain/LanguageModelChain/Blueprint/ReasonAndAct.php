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
                    (?P<thought>.+?)\s*         # Capture the thought before "Action:"
                    Action:\s*                  # Match "Action:" followed by optional whitespace
                    (?P<action>.+?)             # Capture the non-empty action
                    \n
                    Action\ input:\s*           # Match "Action Input:" followed by optional whitespace
                    (?P<input>.*)               # Capture the action input
                )
                |                               # OR
                (?:
                    (?P<final_thought>.+?)\s*   # Capture the thought before "Final Answer:"
                    Final\ answer:\s*           # Match "Final Answer:" followed by optional whitespace
                    (?P<final_answer>.+)        # Capture the non-empty final answer
                )
            /isx'                                // i means case insensitive, s makes . match newlines, x enables free-spacing mode
        );
    }

    /**
     * @return array<string>
     */
    public function stops(): array
    {
        return ['Observation:'];
    }
}
