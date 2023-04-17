<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class CommandRunnerExceptionTest extends TestCase
{
    public function testFromException(): void
    {
        $originalException = new \Exception('Original exception message');
        $commandRunnerException = CommandRunnerException::fromException($originalException);

        $this->assertInstanceOf(CommandRunnerException::class, $commandRunnerException);
        $this->assertSame('Command failed: Original exception message', $commandRunnerException->getMessage());
        $this->assertSame($originalException, $commandRunnerException->getPrevious());
    }
}
