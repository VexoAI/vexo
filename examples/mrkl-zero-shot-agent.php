<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use Vexo\Agent\MRKL\ZeroShotAgent;
use Vexo\Agent\MRKL\ZeroShotAgentExecutor;
use Vexo\Chain\Input;
use Vexo\Event\SomethingHappened;
use Vexo\LanguageModel\OpenAIChatLanguageModel;
use Vexo\Tool\Callback;
use Vexo\Tool\GoogleSearch;
use Vexo\Tool\Resolver\NameResolver;
use Vexo\Tool\Tools;

require __DIR__ . '/../vendor/autoload.php';

$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(SomethingHappened::class, 'dump');

$google = new \Google\Client();
$google->setApplicationName('Vexo');
$google->setDeveloperKey(getenv('GOOGLE_API_KEY'));

$tools = new Tools();
$googleSearchTool = new GoogleSearch(
    new \Google\Service\CustomSearchAPI($google),
    getenv('GOOGLE_CUSTOM_SEARCH_ENGINE_ID')
);
$googleSearchTool->useEventDispatcher($eventDispatcher);
$tools->add($googleSearchTool);

$calculatorTool = new Callback(
    'calculator',
    'Useful for doing math',
    fn (string $input) => 'The answer is 42'
);
$calculatorTool->useEventDispatcher($eventDispatcher);
$tools->add($calculatorTool);

$toolResolver = new NameResolver($tools);

$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();

$llm = new OpenAIChatLanguageModel($chat);
$llm->useEventDispatcher($eventDispatcher);

$agent = ZeroShotAgent::fromLLMAndTools($llm, $tools, $eventDispatcher);

$executor = new ZeroShotAgentExecutor($agent, $toolResolver);
$executor->useEventDispatcher($eventDispatcher);

$output = $executor->process(new Input(['question' => 'What is the weather in Amsterdam?']));

echo "\n{$output->get('result')}\n\n";
