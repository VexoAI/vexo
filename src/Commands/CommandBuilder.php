<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;

final class CommandBuilder
{
    public function __construct(private array $commandNamespaces)
    {
    }

    public function fromArray(array $command): Command
    {
        Ensure::keyExists($command, 'name');
        Ensure::keyExists($command, 'args');

        $commandClass = $this->getCommandClass($command['name']);

        return $commandClass::fromArray($command['args']);
    }

    private function getCommandClass(string $name): string
    {
        foreach ($this->commandNamespaces as $namespace) {
            $commandClass = $namespace . $this->toCamelCase($name);
            if (class_exists($commandClass) && is_subclass_of($commandClass, Command::class)) {
                return $commandClass;
            }
        }

        throw new \RuntimeException(
            sprintf('Command class for %s could not be found in any of the provided namespaces, or it does not implement the Command interface', $name)
        );
    }

    private function toCamelCase(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }
}