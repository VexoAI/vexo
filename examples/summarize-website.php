<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\LanguageModelChain;
use Vexo\Chain\LanguageModelChain\Prompt\StrReplaceRenderer;
use Vexo\Chain\SequentialChain\SequentialChain;
use Vexo\Chain\WebTextChain\WebTextChain;
use Vexo\Contract\Event\Event;
use Vexo\Model\Language\OpenAIChatModel;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('OPENAI_API_KEY')) {
    echo "Not all required environment variables set!\n";
    echo "Please set OPENAI_API_KEY\n\n";
    exit(1);
}

if ($argc <= 1) {
    echo "Please provide a URL as argument!\n\n";
    exit(1);
}

// Load our event dispatcher which will be used to dump events during execution
$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(Event::class, 'dump');

// Load our language model using OpenAI
$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();
$languageModel = new OpenAIChatModel($chat, eventDispatcher: $eventDispatcher);

// Initialize our sequential chain and the two needed chains in order
$sequentialChain = new SequentialChain(
    eventDispatcher: $eventDispatcher,
    chains: [
        // Create our WebTextChain which will take a URL from the context, downloads the
        // web page, and saves the text contents in the context
        new WebTextChain(),

        // Create our LanguageModelChain using the language model above and a basic prompt
        // renderer, which will ask the language model to summarize the given text.
        new LanguageModelChain(
            languageModel: $languageModel,
            promptRenderer: new StrReplaceRenderer("Summarize the text below:\n\n{{text}}")
        )
    ]
);

// Run the chains
$context = new Context(['url' => $argv[1]]);
$sequentialChain->run($context);

dump($context->get('generation'));
