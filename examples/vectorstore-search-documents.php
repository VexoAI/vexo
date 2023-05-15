<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Vexo\Model\Embedding\OpenAIModel;
use Vexo\VectorStore\InMemoryVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('VECTORSTORE_FILENAME')) {
    echo "Not all required environment variables set!\n";
    echo "Please set VECTORSTORE_FILENAME\n\n";
    exit(1);
}

if ($argc <= 1) {
    echo "Please provide a search term as argument!\n\n";
    exit(1);
}

// Load our embedding model
$embeddings = $chat = \OpenAI::client((string) getenv('OPENAI_API_KEY'))->embeddings();
$embeddingModel = new OpenAIModel($embeddings);

// Initialize our vector store.
$vectorStore = new InMemoryVectorStore(embeddingModel: $embeddingModel);
$vectorStore->restoreFromFile(
    filesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__ . '/../tmp')),
    path: getenv('VECTORSTORE_FILENAME')
);

// Search the vector store for similar documents
$documents = $vectorStore->search($argv[1]);
dump($documents);
