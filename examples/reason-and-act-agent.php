<?php

declare(strict_types=1);

namespace Vexo\Examples;

use Dotenv\Dotenv;
use League\Event\EventDispatcher;
use Vexo\Agent\AutonomousExecutor;
use Vexo\Agent\ReasonAndActAgent;
use Vexo\Agent\Tool\GoogleSearch;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\Blueprint\ReasonAndAct;
use Vexo\Chain\LanguageModelChain\LanguageModelChainFactory;
use Vexo\Chain\SequentialChain\SequentialChain;
use Vexo\Contract\Event\Event;
use Vexo\Model\Language\OpenAIChatModel;

require __DIR__ . '/../vendor/autoload.php';

if ($argc <= 1) {
    echo "Please provide a question as argument!\n\n";
    exit(1);
}

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['OPENAI_API_KEY', 'OPENAI_CHAT_MODEL', 'GOOGLE_API_KEY', 'GOOGLE_CUSTOM_SEARCH_ENGINE_ID']);

$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(Event::class, 'dump');

$google = new \Google\Client();
$google->setApplicationName('Vexo');
$google->setDeveloperKey($_ENV['GOOGLE_API_KEY']);

$tools = new Tools([
    new GoogleSearch(
        new \Google\Service\CustomSearchAPI($google),
        $_ENV['GOOGLE_CUSTOM_SEARCH_ENGINE_ID']
    )
]);

$chat = \OpenAI::client($_ENV['OPENAI_API_KEY'])->chat();
$languageModel = new OpenAIChatModel(
    chat: $chat,
    parameters: ['model' => $_ENV['OPENAI_CHAT_MODEL']],
    eventDispatcher: $eventDispatcher
);
$sequentialChain = new SequentialChain(
    $eventDispatcher,
    [(new LanguageModelChainFactory($languageModel))->createFromBlueprint(new ReasonAndAct())]
);

$agent = new ReasonAndActAgent($sequentialChain, $tools, $eventDispatcher);
$executor = new AutonomousExecutor(agent: $agent, eventDispatcher: $eventDispatcher);

$context = new Context(['question' => $argv[1]]);
$executor->run($context);

dump($context);
