<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use Vexo\Agent\AutonomousExecutor;
use Vexo\Agent\ReasonAndActAgent;
use Vexo\Agent\Tool\GoogleSearch;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\Blueprint\ReasonAndAct;
use Vexo\Chain\LanguageModelChain\LanguageModelChainFactory;
use Vexo\Chain\SequentialRunner;
use Vexo\Contract\Event\Event;
use Vexo\LanguageModel\OpenAIChatModel;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('OPENAI_API_KEY') || ! getenv('GOOGLE_API_KEY') || ! getenv('GOOGLE_CUSTOM_SEARCH_ENGINE_ID')) {
    echo "Not all required environment variables set!\n";
    echo "Please set OPENAI_API_KEY, GOOGLE_API_KEY and GOOGLE_CUSTOM_SEARCH_ENGINE_ID\n\n";
    exit(1);
}

if ($argc <= 1) {
    echo "Please provide a question as argument!\n\n";
    exit(1);
}

$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(Event::class, 'dump');

$google = new \Google\Client();
$google->setApplicationName('Vexo');
$google->setDeveloperKey(getenv('GOOGLE_API_KEY'));

$tools = new Tools([
    new GoogleSearch(
        new \Google\Service\CustomSearchAPI($google),
        getenv('GOOGLE_CUSTOM_SEARCH_ENGINE_ID')
    )
]);

$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();
$languageModel = new OpenAIChatModel($chat, eventDispatcher: $eventDispatcher);
$languageModelChainRunner = new SequentialRunner(
    $eventDispatcher,
    [(new LanguageModelChainFactory($languageModel))->createFromBlueprint(new ReasonAndAct())]
);

$agent = new ReasonAndActAgent($languageModelChainRunner, $tools, $eventDispatcher);
$executor = new AutonomousExecutor(agent: $agent, eventDispatcher: $eventDispatcher);

$context = new Context(['question' => $argv[1]]);
$executor->run($context);

dump($context);
