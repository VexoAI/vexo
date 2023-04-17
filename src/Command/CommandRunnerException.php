<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

final class CommandRunnerException extends \RuntimeException
{
    public static function fromException(\Exception $e): CommandRunnerException
    {
        return new CommandRunnerException('Command failed: ' . $e->getMessage(), 0, $e);
    }
}