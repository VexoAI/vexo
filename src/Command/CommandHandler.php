<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

interface CommandHandler
{
    public function handles(): array;

    public function handle(Command $command): CommandResult;
}