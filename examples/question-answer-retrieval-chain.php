<?php

declare(strict_types=1);

namespace Vexo\Examples;

use Dotenv\Dotenv;
use League\Event\EventDispatcher;
use Probots\Pinecone\Client as Pinecone;
use Vexo\Chain\ConcatenateDocumentsChain\ConcatenateDocumentsChain;
use Vexo\Chain\Context;
use Vexo\Chain\DocumentsRetrieverChain\DocumentsRetrieverChain;
use Vexo\Chain\LanguageModelChain\Blueprint\AnswerQuestionAboutContext;
use Vexo\Chain\LanguageModelChain\LanguageModelChainFactory;
use Vexo\Chain\SequentialChain\SequentialChain;
use Vexo\Contract\Event;
use Vexo\Document\Retriever\VectorStoreRetriever;
use Vexo\Model\Embedding\OpenAIModel;
use Vexo\Model\Language\OpenAIChatModel;
use Vexo\VectorStore\PineconeVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ($argc <= 1) {
    echo "Please provide a question as argument!\n\n";
    exit(1);
}

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['OPENAI_API_KEY', 'PINECONE_API_KEY', 'PINECONE_ENVIRONMENT', 'PINECONE_INDEX_NAME']);

// Load our event dispatcher which will be used to dump events during execution
$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(Event::class, 'dump');

$openAI = \OpenAI::client($_ENV['OPENAI_API_KEY']);

// Load our embedding model
$embeddingModel = new OpenAIModel($openAI->embeddings());

// Load our vector store
$pinecone = new Pinecone($_ENV['PINECONE_API_KEY'], $_ENV['PINECONE_ENVIRONMENT']);
$vectorStore = new PineconeVectorStore(
    embeddingModel: $embeddingModel,
    pinecone: $pinecone->index($_ENV['PINECONE_INDEX_NAME'])->vectors()
);

// Load our language model
$languageModel = new OpenAIChatModel($openAI->chat(), eventDispatcher: $eventDispatcher);

// Now we will set up our chains which will be used to answer the question.
//
// The DocumentsRetrieverChain will retrieve the most similar documents from the vector store based on a similarity
// search with the question
//
// The ConcatenateDocumentsChain will concatenate the documents into a single string which will be used as context for
// the language model
//
// Our language model chain factory will create a chain based on the blueprint we provide. The blueprint configures the
// chain with the correct prompt. The language model chain will then answer the question based on the context.
//
$sequentialChain = new SequentialChain(
    eventDispatcher: $eventDispatcher,
    chains: [
        new DocumentsRetrieverChain(
            retriever: new VectorStoreRetriever(vectorStore: $vectorStore),
            maxResults: 3,
            inputMap: ['query' => 'question'] // Make sure question is also available as query
        ),
        new ConcatenateDocumentsChain(),
        (new LanguageModelChainFactory($languageModel))->createFromBlueprint(
            new AnswerQuestionAboutContext(),
            inputMap: ['context' => 'combined_contents'] // Make sure combined_contents is also available as context
        )
    ]
);

// Ask the question
$context = new Context(['question' => $argv[1]]);
$sequentialChain->run($context);

dump($context->get('answer'));
