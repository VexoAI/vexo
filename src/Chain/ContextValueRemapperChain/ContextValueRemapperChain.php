<?php

declare(strict_types=1);

namespace Vexo\Chain\ContextValueRemapperChain;

use Vexo\Chain\Attribute\RequiresContextValuesMethod;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;

#[RequiresContextValuesMethod('requiredContextValues')]
final class ContextValueRemapperChain implements Chain
{
    /**
     * @param array<string, string> $remappings
     */
    public function __construct(
        private readonly array $remappings
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function requiredContextValues(): array
    {
        return array_fill_keys(array_keys($this->remappings), 'mixed');
    }

    public function run(Context $context): void
    {
        foreach ($this->remappings as $from => $to) {
            $context->put($to, $context->get($from));
        }
    }
}
