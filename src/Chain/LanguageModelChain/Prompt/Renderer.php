<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Vexo\Chain\Context;

interface Renderer
{
    public function render(Context $context): string;
}
