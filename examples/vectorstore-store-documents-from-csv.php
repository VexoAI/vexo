<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Vexo\DocumentLoader\CsvFileLoader;
use Vexo\EmbeddingModel\OpenAIModel;
use Vexo\VectorStore\DocumentAdded;
use Vexo\VectorStore\InMemoryVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('OPENAI_API_KEY') || ! getenv('CSV_FILE_PATH') || ! getenv('VECTORSTORE_FILENAME')) {
    echo "Not all required environment variables set!\n";
    echo "Please set OPENAI_API_KEY, CSV_FILE_PATH, VECTORSTORE_FILENAME\n\n";
    exit(1);
}

// Load our event dispatcher which will be used to dump events during execution
$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(
    DocumentAdded::class,
    function (DocumentAdded $event): void {
        $memoryUsage = memory_get_usage();
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $memoryUsage > 0 ? floor(log($memoryUsage, 1024)) : 0;
        $readableMemoryUsage = number_format($memoryUsage / 1024 ** $power, 2, '.', ',') . ' ' . $units[$power];

        dump($event->document, $readableMemoryUsage);
    }
);

// Load our embedding model
$embeddings = $chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->embeddings();
$embeddingModel = new OpenAIModel($embeddings);

// Initialize our vector store.
//
// Note that you will need to tweak the amount of hyperplanes to use for your specific use case. The more hyperplanes
// you use, the faster the search will be, but at the cost of accuracy. Using 0 hyperplanes will effectively disable the
// Locality Sensitive Hashing (LSH) algorithm, but will mean terrible performance for even small datasets.
//
$vectorStore = new InMemoryVectorStore(embeddingModel: $embeddingModel, numHyperplanes: 20);
$vectorStore->useEventDispatcher($eventDispatcher);

// Load our CSV file loader
$fileLoader = new CsvFileLoader(
    path: getenv('CSV_FILE_PATH'),
    contentsColumn: 'content' // The column in the CSV file that contains the text we want to store
);

// Load the documents from the CSV file into the vector store
foreach ($fileLoader->load() as $document) {
    $vectorStore->add($document, ['title']);
}

// Persist the vector store to disk
$vectorStore->persistToFile(
    filesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__ . '/../tmp')),
    path: getenv('VECTORSTORE_FILENAME')
);
