<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

interface LanguageModel
{
    /**
     * @param array<string> $stops
     */
    public function generate(string $prompt, array $stops = []): Result;
}
