<?php

declare(strict_types=1);

namespace Vexo\Chain\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class RequiresContextValue
{
    public const CONTEXT_VALUE_TYPES = [
        'string',
        'int',
        'integer',
        'float',
        'bool',
        'array',
        'object',
        'mixed',
    ];

    /**
     * @param value-of<self::CONTEXT_VALUE_TYPES>|class-string $type
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type
    ) {
    }
}
