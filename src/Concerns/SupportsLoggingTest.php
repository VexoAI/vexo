<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

#[CoversClass(SupportsLogging::class)]
final class SupportsLoggingTest extends TestCase
{
    public function testSetLoggerAndGetLogger(): void
    {
        $supportsLogging = new SupportsLoggingSUT();
        $customLogger = new LoggerStub();

        $supportsLogging->setLogger($customLogger);
        $logger = $supportsLogging->logger();

        $this->assertSame($customLogger, $logger);
    }

    public function testDefaultLogger(): void
    {
        $supportsLogging = new SupportsLoggingSUT();

        $logger = $supportsLogging->logger();

        $this->assertInstanceOf(NullLogger::class, $logger);
    }
}

final class SupportsLoggingSUT
{
    use SupportsLogging;
}

final class LoggerStub extends NullLogger implements LoggerInterface
{
}
