<?php

declare(strict_types=1);

namespace Vexo\Chain\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class RequiresContextValuesMethod
{
    public function __construct(
        public readonly string $methodName
    ) {
    }
}
