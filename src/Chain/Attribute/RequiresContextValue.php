<?php

declare(strict_types=1);

namespace Vexo\Chain\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class RequiresContextValue
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $type = null
    ) {
    }
}
