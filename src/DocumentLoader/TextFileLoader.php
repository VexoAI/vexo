<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemReader;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;

final class TextFileLoader implements Loader
{
    public function __construct(
        private readonly FilesystemReader $filesystem,
        private readonly string $path
    ) {
    }

    public function load(): DocumentsContract
    {
        try {
            $contents = $this->filesystem->read($this->path);
        } catch (FilesystemException $e) {
            throw new SorryFailedToLoadDocument($e->getMessage(), $e->getCode(), $e);
        }

        return new Documents(
            [new Document($contents, new Metadata(['path' => $this->path]))]
        );
    }
}
