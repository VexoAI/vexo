<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use League\Csv\Reader;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;

final class CsvFileLoader implements Loader
{
    private readonly Reader $reader;

    public function __construct(
        private readonly string $path,
        private readonly string $contentsColumn = 'contents',
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\',
        int $headerOffset = 0
    ) {
        $this->reader = Reader::createFromPath($path)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape)
            ->setHeaderOffset($headerOffset);
    }

    public function load(): DocumentsContract
    {
        $documents = new Documents();

        try {
            $records = $this->reader->getRecords();
            foreach ($records as $offset => $record) {
                $documents[] = new Document(
                    $record[$this->contentsColumn],
                    new Metadata([
                        'path' => $this->path,
                        'offset' => $offset,
                        ...array_diff_key($record, [$this->contentsColumn => null])
                    ])
                );
            }
        } catch (\Throwable $e) {
            throw new FailedToLoadDocument($e->getMessage(), $e->getCode(), $e);
        }

        return $documents;
    }
}