<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Webmozart\Assert\Assert;

final class ContextAssert extends Assert
{
    protected static function reportInvalidArgument($message): void
    {
        throw new FailedToValidateContextValue($message);
    }
}
