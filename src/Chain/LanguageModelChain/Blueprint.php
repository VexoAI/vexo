<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;

interface Blueprint
{
    public function promptRenderer(): Renderer;

    public function outputParser(): OutputParser;

    /**
     * @return array<string>
     */
    public function requiredContextValues(): array;

    /**
     * @return array<string>
     */
    public function stops(): array;
}
