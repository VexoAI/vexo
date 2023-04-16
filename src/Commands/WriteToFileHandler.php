<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;
use League\Flysystem\Filesystem;

final class WriteToFileHandler implements CommandHandler
{
    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function handles(): array
    {
        return [WriteToFile::class];
    }

    public function handle(Command $command): CommandResult
    {
        Ensure::isInstanceOf($command, WriteToFile::class);

        $this->filesystem->write($command->file, $command->contents);

        return new CommandResult(['Contents written successfully']);
    }
}