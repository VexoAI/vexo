<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Vexo\Chain\ConcatenateDocumentsChain\ConcatenateDocumentsChain;
use Vexo\Chain\Context;
use Vexo\Chain\ContextValueRemapperChain\ContextValueRemapperChain;
use Vexo\Chain\DocumentsRetrieverChain\DocumentsRetrieverChain;
use Vexo\Chain\DocumentsRetrieverChain\Retriever\VectorStoreRetriever;
use Vexo\Chain\LanguageModelChain\Blueprint\AnswerQuestionAboutContext;
use Vexo\Chain\LanguageModelChain\LanguageModelChainFactory;
use Vexo\Chain\SequentialRunner;
use Vexo\Contract\Event\Event;
use Vexo\EmbeddingModel\OpenAIModel;
use Vexo\LanguageModel\OpenAIChatLanguageModel;
use Vexo\VectorStore\InMemoryVectorStore;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('OPENAI_API_KEY') || ! getenv('VECTORSTORE_FILENAME')) {
    echo "Not all required environment variables set!\n";
    echo "Please set OPENAI_API_KEY, VECTORSTORE_FILENAME\n\n";
    exit(1);
}

if ($argc <= 1) {
    echo "Please provide a question as argument!\n\n";
    exit(1);
}

// Load our event dispatcher which will be used to dump events during execution
$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(Event::class, 'dump');

$openAI = \OpenAI::client(getenv('OPENAI_API_KEY'));

// Load our embedding model
$embeddingModel = new OpenAIModel($openAI->embeddings());

// Initialize our vector store.
$vectorStore = new InMemoryVectorStore(embeddingModel: $embeddingModel);
$vectorStore->restoreFromFile(
    filesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__ . '/../tmp')),
    path: getenv('VECTORSTORE_FILENAME')
);

// Load our language model
$languageModel = new OpenAIChatLanguageModel($openAI->chat());

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
$runner = new SequentialRunner(
    eventDispatcher: $eventDispatcher,
    chains: [
        new ContextValueRemapperChain(['question' => 'query']), // Make sure question is also available as query
        new DocumentsRetrieverChain(
            new VectorStoreRetriever(vectorStore: $vectorStore, numResults: 3)
        ),
        new ConcatenateDocumentsChain(),
        new ContextValueRemapperChain(['combined_contents' => 'context']), // Make sure combined_contents is also available as context
        (new LanguageModelChainFactory($languageModel))->createFromBlueprint(
            new AnswerQuestionAboutContext()
        )
    ]
);

// Ask the question
$context = new Context(['question' => $argv[1]]);
$runner->run($context);

dump($context->get('answer'));
