<?php

declare(strict_types=1);

namespace Vexo\Document\Loader;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemReader;
use Vexo\Contract\Metadata;
use Vexo\Document\Document;
use Vexo\Document\Documents;

final class TextFileLoader implements Loader
{
    public function __construct(
        private readonly FilesystemReader $filesystem,
        private readonly string $path
    ) {
    }

    public function load(): Documents
    {
        try {
            $contents = $this->filesystem->read($this->path);
        } catch (FilesystemException $e) {
            throw new FailedToLoadDocument($e->getMessage(), $e->getCode(), $e);
        }

        return new Documents(
            [new Document($contents, new Metadata(['path' => $this->path]))]
        );
    }
}
