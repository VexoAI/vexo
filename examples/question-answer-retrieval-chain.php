<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Vexo\Chain\Chains;
use Vexo\Chain\ConcatenateDocumentsChain\ConcatenateDocumentsChain;
use Vexo\Chain\DocumentsRetrieverChain\DocumentsRetrieverChain;
use Vexo\Chain\Input;
use Vexo\Chain\LanguageModelChain\Blueprint\QuestionAnswerBlueprint;
use Vexo\Chain\LanguageModelChain\LanguageModelChainFactory;
use Vexo\Chain\SequentialChain\SequentialChain;
use Vexo\Contract\Event\SomethingHappened;
use Vexo\EmbeddingModel\OpenAIModel;
use Vexo\LanguageModel\OpenAIChatLanguageModel;
use Vexo\Retriever\VectorStoreRetriever;
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
$eventDispatcher->subscribeTo(SomethingHappened::class, 'dump');

$openAI = \OpenAI::client(getenv('OPENAI_API_KEY'));

// Load our embedding model
$embeddingModel = new OpenAIModel($openAI->embeddings());

// Initialize our vector store.
$vectorStore = new InMemoryVectorStore(embeddingModel: $embeddingModel);
$vectorStore->useEventDispatcher($eventDispatcher);
$vectorStore->restoreFromFile(
    filesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__ . '/../tmp')),
    path: getenv('VECTORSTORE_FILENAME')
);

// Load our language model
$languageModel = new OpenAIChatLanguageModel($openAI->chat());
$languageModel->useEventDispatcher($eventDispatcher);

// Now we will set up our chains which will be used to answer the question

// The DocumentsRetrieverChain will retrieve the most similar documents from the vector store based on a similarity
// search with the question
$documentsRetrieverChain = new DocumentsRetrieverChain(
    new VectorStoreRetriever(vectorStore: $vectorStore, numResults: 3),
    inputKey: 'question',
    outputKey: 'documents'
);
$documentsRetrieverChain->useEventDispatcher($eventDispatcher);

// The ConcatenateDocumentsChain will concatenate the documents into a single string which will be used as context for
// the language model
$concatenateDocumentsChain = new ConcatenateDocumentsChain(
    inputKey: 'documents',
    outputKey: 'context'
);
$concatenateDocumentsChain->useEventDispatcher($eventDispatcher);

// Our language model chain factory will create a chain based on the blueprint we provide. The blueprint configures the
// chain with the correct prompt and input/output keys
$languageModelChainFactory = new LanguageModelChainFactory($languageModel);
$languageModelChain = $languageModelChainFactory->createFromBlueprint(
    new QuestionAnswerBlueprint()
);
$languageModelChain->useEventDispatcher($eventDispatcher);

// Create a sequential chain containing the three chains above
$sequentialChain = new SequentialChain(
    new Chains([$documentsRetrieverChain, $concatenateDocumentsChain, $languageModelChain]),
    inputKeys: ['question'],
    outputKeys: ['answer'],
    outputAll: true
);
$sequentialChain->useEventDispatcher($eventDispatcher);

// Ask the question
$output = $sequentialChain->process(
    new Input(['question' => $argv[1]])
);

dump($output);
