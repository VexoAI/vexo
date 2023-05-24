<?php

declare(strict_types=1);

namespace Vexo\Examples;

use Dotenv\Dotenv;
use Probots\Pinecone\Client as Pinecone;
use Vexo\Model\Embedding\OpenAIModel;
use Vexo\VectorStore\PineconeVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ($argc <= 1) {
    echo "Please provide a search term as argument!\n\n";
    exit(1);
}

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['OPENAI_API_KEY', 'PINECONE_API_KEY', 'PINECONE_ENVIRONMENT', 'PINECONE_INDEX_NAME']);

// Load our embedding model
$embeddings = $chat = \OpenAI::client($_ENV['OPENAI_API_KEY'])->embeddings();
$embeddingModel = new OpenAIModel($embeddings);

// Load our vector store
$pinecone = new Pinecone($_ENV['PINECONE_API_KEY'], $_ENV['PINECONE_ENVIRONMENT']);
$vectorStore = new PineconeVectorStore(
    embeddingModel: $embeddingModel,
    pinecone: $pinecone->index($_ENV['PINECONE_INDEX_NAME'])->vectors()
);

// Search the vector store for similar documents
$documents = $vectorStore->similaritySearch($argv[1]);
dump($documents);
