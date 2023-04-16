<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;
use League\Flysystem\Filesystem;

final class ReadFromFileHandler implements CommandHandler
{
    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function handles(): array
    {
        return [ReadFromFile::class];
    }

    public function handle(Command $command): CommandResult
    {
        Ensure::isInstanceOf($command, ReadFromFile::class);

        return new CommandResult(['contents' => $this->filesystem->read($command->file)]);
    }
}