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
use Vexo\Tool\CallableTool;

require __DIR__ . '/../vendor/autoload.php';

$logger = new Logger('weave');
$logger->pushHandler(new ConsoleHandler(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG)));

$eventDispatcher = new EventDispatcher();
$eventDispatcher->subscribeTo(
    SomethingHappened::class,
    function (SomethingHappened $event) use ($logger) {
        $logger->debug(get_class($event), $event->payload());
    }
);

$tools = [
    'google' => new CallableTool(
        'google',
        'Search the internet',
        function (string $input) {
            return 'The current weather is 22 degrees celsius, cloudy with a chance of rain';
        }
    ),
    'calculator' => new CallableTool(
        'calculator',
        'Useful for doing math',
        function (string $input) {
            return 'The answer is 42';
        }
    ),
];
$tools['google']->useEventDispatcher($eventDispatcher);
$tools['calculator']->useEventDispatcher($eventDispatcher);

$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();

$llm = new OpenAIChatLLM($chat);
$llm->useEventDispatcher($eventDispatcher);

$agent = ZeroShotAgent::fromLLMAndTools($llm, $tools, $eventDispatcher);

$executor = new ZeroShotAgentExecutor($agent, $tools);
$executor->useEventDispatcher($eventDispatcher);

$output = $executor->process(new Input(['question' => 'What is the weather in Amsterdam?']));
echo PHP_EOL . PHP_EOL . $output->get('result') . PHP_EOL;
