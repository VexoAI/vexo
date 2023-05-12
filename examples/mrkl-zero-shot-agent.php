<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use Vexo\Agent\MRKL\ZeroShotAgent;
use Vexo\Agent\MRKL\ZeroShotAgentExecutor;
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\GoogleSearch;
use Vexo\Agent\Tool\Resolver\NameResolver;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;
use Vexo\LanguageModel\OpenAIChatLanguageModel;

require __DIR__ . '/../vendor/autoload.php';

if ( ! getenv('OPENAI_API_KEY') || ! getenv('GOOGLE_API_KEY') || ! getenv('GOOGLE_API_KEY')) {
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
    ),
    new Callback(
        'calculator',
        'Useful for doing math',
        fn (string $input): string => 'The answer is 42'
    )
]);

$toolResolver = new NameResolver($tools);

$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();

$llm = new OpenAIChatLanguageModel($chat);

$agent = ZeroShotAgent::fromLLMAndTools($llm, $tools, $eventDispatcher);

$executor = new ZeroShotAgentExecutor($agent, $toolResolver);
$executor->useEventDispatcher($eventDispatcher);

$context = new Context(['question' => $argv[1]]);
$executor->run($context);

dump($context->get('result'));
