<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use Assert\Assertion as Ensure;

final class DoNothingHandler implements CommandHandler
{
    public function handles(): array
    {
        return [DoNothing::class];
    }

    public function handle(Command $command): CommandResult
    {
        Ensure::isInstanceOf($command, DoNothing::class);

        return new CommandResult([]);
    }
}