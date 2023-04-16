<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;

final class CommandRunner
{
    private array $handlers;

    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            Ensure::isInstanceOf($handler, CommandHandler::class);
            $this->registerHandler($handler);
        }
    }

    public function registerHandler(CommandHandler $handler)
    {
        foreach ($handler->handles() as $class) {
            $this->handlers[$class] = $handler;
        }
    }

    public function handle(Command $command): CommandResult
    {
        try {
            $result = $this->resolve($command)->handle($command);
        } catch (\Exception $e) {
            throw CommandRunnerException::fromException($e);
        }
        return $result;
    }

    private function resolve(Command $command): CommandHandler
    {
        if (!isset($this->handlers[$command::class])) {
            throw new \InvalidArgumentException(
                sprintf('No handler registered for command %s', $command::class)
            );
        }

        return $this->handlers[$command::class];
    }
}