<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class CommandRunnerTest extends TestCase
{
    public function testRegisterHandler(): void
    {
        $handler = new DoNothingHandler();

        $runner = new CommandRunner([$handler]);

        $command = new DoNothing('Test reason');
        $result = $runner->handle($command);

        $this->assertInstanceOf(CommandResult::class, $result);
    }

    public function testHandleWithUnregisteredCommand(): void
    {
        $this->expectException(CommandRunnerException::class);
        $this->expectExceptionMessage('No handler registered for command Pragmatist\Assistant\Command\UnregisteredCommand');

        $handler = new DoNothingHandler();

        $runner = new CommandRunner([$handler]);

        $command = UnregisteredCommand::fromArray(['test_arg' => 'test_value']);
        $runner->handle($command);
    }

    public function testHandleWithError(): void
    {
        $this->expectException(CommandRunnerException::class);

        $handler = new FailingHandler();

        $runner = new CommandRunner([$handler]);

        $command = new DoNothing('Test reason');
        $runner->handle($command);
    }
}

class UnregisteredCommand implements Command
{
    private array $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public static function fromArray(array $arguments): Command
    {
        return new self($arguments);
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}

class FailingHandler implements CommandHandler
{
    public function handles(): array
    {
        return [DoNothingCommand::class];
    }

    public function handle(Command $command): CommandResult
    {
        throw new \Exception('Error handling command');
    }
}
