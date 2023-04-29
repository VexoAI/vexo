<?php

declare(strict_types=1);

namespace Vexo\Model;

use Vexo\Prompt\Prompt;

interface LanguageModel
{
    public function generate(Prompt $prompt, string ...$stops): Response;
}
