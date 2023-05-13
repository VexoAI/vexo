<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

interface LanguageModel
{
    public function generate(string $prompt, string ...$stops): Response;
}
