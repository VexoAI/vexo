<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Documents;

final class DirectoryLoader implements Loader
{
    /**
     * @var callable
     */
    private $filter;

    /**
     * @var callable
     */
    private $fileLoader;

    public function __construct(
        private readonly FilesystemReader $filesystem,
        private readonly string $path,
        private readonly bool $loadRecursive = false,
        ?callable $filter = null,
        ?callable $fileLoader = null
    ) {
        $this->filter = $filter ?? fn (StorageAttributes $attributes): bool => true;
        $this->fileLoader = $fileLoader ?? fn (FilesystemReader $filesystem, string $path): DocumentsContract => (new TextFileLoader($filesystem, $path))->load();
    }

    public function load(): DocumentsContract
    {
        $documents = new Documents();

        $directoryListing = $this->filesystem->listContents($this->path, $this->loadRecursive)
            ->filter(fn (StorageAttributes $attributes): bool => $attributes->isFile())
            ->filter($this->filter);

        try {
            foreach ($directoryListing as $file) {
                /** @var DocumentsContract $documents */
                $documents = $documents->merge(
                    ($this->fileLoader)($this->filesystem, $file->path())
                );
            }
        } catch (\Throwable $e) {
            throw new SorryFailedToLoadDocument($e->getMessage(), $e->getCode(), $e);
        }

        if ($documents->isEmpty()) {
            throw new SorryFailedToLoadDocument('No documents found in directory');
        }

        return $documents;
    }
}