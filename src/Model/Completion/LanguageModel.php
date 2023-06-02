<?php

declare(strict_types=1);

namespace Vexo\Model\Completion;

interface LanguageModel
{
    /**
     * @param array<string> $stops
     */
    public function generate(string $prompt, array $stops = []): Result;
}
