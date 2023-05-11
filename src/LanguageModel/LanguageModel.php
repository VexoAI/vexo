<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\LanguageModel\Prompt\Prompt;

interface LanguageModel
{
    public function generate(Prompt $prompt, string ...$stops): Response;
}
