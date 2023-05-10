<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Vexo\EmbeddingModel\OpenAIModel;
use Vexo\VectorStore\DocumentAdded;
use Vexo\VectorStore\InMemoryVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('VECTORSTORE_FILENAME')) {
    echo "Not all required environment variables set!\n";
    echo "Please set VECTORSTORE_FILENAME\n\n";
    exit(1);
}

// Load our event dispatcher which will be used to dump events during execution
$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(DocumentAdded::class, 'dump');

// Load our embedding model
$embeddings = $chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->embeddings();
$embeddingModel = new OpenAIModel($embeddings);

// Initialize our vector store.
$vectorStore = new InMemoryVectorStore(embeddingModel: $embeddingModel);
$vectorStore->useEventDispatcher($eventDispatcher);
$vectorStore->restoreFromFile(
    filesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__ . '/../tmp')),
    path: getenv('VECTORSTORE_FILENAME')
);

// Search the vector store for similar documents
$documents = $vectorStore->search('google');
dump($documents);
