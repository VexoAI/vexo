<?php

declare(strict_types=1);

namespace Vexo\Weave\Examples;

use Monolog\Level;
use Monolog\Logger;
use Vexo\Weave\Agent\MRKL\ZeroShotAgent;
use Vexo\Weave\Agent\MRKL\ZeroShotAgentExecutor;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\LLM\OpenAIChatLLM;
use Vexo\Weave\Tool\CallableTool;

require __DIR__ . '/../vendor/autoload.php';

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

$logger = new Logger('weave');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', Level::Debug));

$chat = \OpenAI::client(getenv('OPENAI_API_KEY'))->chat();

$llm = new OpenAIChatLLM($chat);
$llm->setLogger($logger);

$agent = ZeroShotAgent::fromLLMAndTools($llm, ...array_values($tools));
$agent->setLogger($logger);

$executor = new ZeroShotAgentExecutor($agent, $tools);
$executor->setLogger($logger);

$output = $executor->process(new Input(['question' => 'What is the weather in Amsterdam?']));
echo PHP_EOL . PHP_EOL . $output->get('result') . PHP_EOL;
