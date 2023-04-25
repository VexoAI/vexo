<?php

declare(strict_types=1);

namespace Vexo\Examples;

use League\Event\EventDispatcher;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Vexo\Agent\MRKL\ZeroShotAgent;
use Vexo\Agent\MRKL\ZeroShotAgentExecutor;
use Vexo\Chain\Input;
use Vexo\LLM\OpenAIChatLLM;
use Vexo\SomethingHappened;
use Vexo\Tool\Callback;
use Vexo\Tool\GoogleSearch;
use Vexo\Tool\Resolver\NameResolver;
use Vexo\Tool\Tools;

require __DIR__ . '/../vendor/autoload.php';

$logger = new Logger('weave');
$logger->pushHandler(new ConsoleHandler(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG)));

$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(
    SomethingHappened::class,
    function (SomethingHappened $event) use ($logger): void {
        $logger->debug($event::class, $event->payload());
    }
);

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

$llm = new OpenAIChatLLM($chat);
$llm->useEventDispatcher($eventDispatcher);

$agent = ZeroShotAgent::fromLLMAndTools($llm, $tools, $eventDispatcher);

$executor = new ZeroShotAgentExecutor($agent, $toolResolver);
$executor->useEventDispatcher($eventDispatcher);

$output = $executor->process(new Input(['question' => 'What is the weather in Amsterdam?']));
echo "\n{$output->get('result')}\n\n";
