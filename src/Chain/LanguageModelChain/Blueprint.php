<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

interface Blueprint
{
    public function promptTemplate(): string;

    /**
     * @return array<string>
     */
    public function promptVariables(): array;

    /**
     * @return array<string>
     */
    public function stops(): array;
}
