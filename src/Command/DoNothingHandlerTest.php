<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class DoNothingHandlerTest extends TestCase
{
    private DoNothingHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new DoNothingHandler();
    }

    public function testHandlesReturnsCorrectCommandClasses(): void
    {
        $this->assertSame([DoNothing::class], $this->handler->handles());
    }

    public function testHandleReturnsCommandResultWithFormattedMessage(): void
    {
        $reason = 'Testing the DoNothingHandler';
        $command = new DoNothing($reason);

        $result = $this->handler->handle($command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertSame([], $result->data);
    }

    public function testHandleThrowsExceptionForInvalidCommand(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidCommand = $this->createMock(Command::class);
        $this->handler->handle($invalidCommand);
    }
}