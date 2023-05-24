<?php

declare(strict_types=1);

namespace Vexo\Examples;

use Dotenv\Dotenv;
use Probots\Pinecone\Client as Pinecone;
use Vexo\Document\Loader\CsvFileLoader;
use Vexo\Model\Embedding\OpenAIModel;
use Vexo\VectorStore\PineconeVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ($argc <= 1) {
    echo "Please provide a path to CSV file as as argument!\n\n";
    exit(1);
}

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['OPENAI_API_KEY', 'PINECONE_API_KEY', 'PINECONE_ENVIRONMENT', 'PINECONE_INDEX_NAME']);

// Load our embedding model
$embeddings = \OpenAI::client($_ENV['OPENAI_API_KEY'])->embeddings();
$embeddingModel = new OpenAIModel($embeddings);

// Load our vector store
$pinecone = new Pinecone($_ENV['PINECONE_API_KEY'], $_ENV['PINECONE_ENVIRONMENT']);
$vectorStore = new PineconeVectorStore(
    embeddingModel: $embeddingModel,
    pinecone: $pinecone->index($_ENV['PINECONE_INDEX_NAME'])->vectors()
);

// Load our CSV file loader
$fileLoader = new CsvFileLoader(
    path: $argv[1],
    contentsColumn: 'content' // The column in the CSV file that contains the text we want to store
);

// Load the documents from the CSV file into the vector store
$documents = $fileLoader->load();
$vectorStore->addDocuments($documents);
