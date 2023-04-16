<?php

declare(strict_types=1);

namespace Pragmatist\Assistant;

use OpenAI\Client as OpenAIClient;
use Nette\Utils\Json;
use Pragmatist\Assistant\Commands\CommandBuilder;
use Pragmatist\Assistant\Commands\CommandRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Application extends Command
{
    const PROMPT = 'You are Assistant-AI, the helpful personal
Your decisions must always be made independently without seeking user assistance. Play to your strengths as an LLM and pursue simple strategies with no legal complications.

GOALS:
1. Find out today\'s featured article on the English Wikipedia

Constraints:
1. ~4000 word limit for short term memory. Your short term memory is short, so immediately save important information to files.
2. If you are unsure how you previously did something or want to recall past events, thinking about similar events will help you remember.
3. No user assistance
4. Exclusively use the commands listed in double quotes e.g. "command name"

Commands:
1. Google Search: "google", args: "query": "<search>"
2. Browse Webpage: "browse_webpage", args: "url": "<url>", "question": "<question_you_want_answered_about_the_webpage>"
3. Write to file: "write_to_file", args: "file": "<file>", "contents": "<file_contents>"
4. Read file: "read_from_file", args: "file": "<file>"
5. Task Complete (Shutdown): "task_complete", args: "reason": "<reason>"
6. Do Nothing: "do_nothing", args: "reason": "<reason>"

Resources:
1. Internet access for searches and information gathering.
2. File output.

Performance Evaluation:
1. Continuously review and analyze your actions to ensure you are performing to the best of your abilities.
2. Constructively self-criticize your big-picture behavior constantly.
3. Reflect on past decisions and strategies to refine your approach.
4. Every command has a cost, so be smart and efficient. Aim to complete tasks in the least number of steps.

You should only respond in JSON format as described below
Response Format:
{
    "thoughts": {
        "text": "thought",
        "reasoning": "reasoning",
        "plan": "- short bulleted\n- list that conveys\n- long-term plan",
        "criticism": "constructive self-criticism",
        "speak": "thoughts summary to say to user"
    },
    "command": {
        "name": "command name",
        "args": {
            "arg name": "value"
        }
    }
}
Ensure the response can be parsed by PHP json_decode';

    public function __construct(
        private OpenAIClient $openAIClient,
        private CommandBuilder $commandBuilder,
        private CommandRunner $commandRunner
    ) {
        parent::__construct('run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $messages = [
            ['role' => 'system', 'content' => self::PROMPT],
            ['role' => 'system', 'content' => 'The current time and date is ' . date('Y-m-d\TH:i:s')],
            ['role' => 'user', 'content' => 'Determine which next command to use, and respond using the format specified above:']
        ];

        $step = 1;
        while (true) {
            $io->title('Iteration ' . $step);

            $io->writeln('<fg=gray>Thinking...</>');
            $io->writeln('');

            $response = $this->openAIClient->chat()->create(['model' => 'gpt-3.5-turbo', 'messages' => $messages]);

            $result = Json::decode($response->choices[0]->message->content, true);

            $messages[] = ['role' => 'assistant', 'content' => Json::encode($result)];

            $io->writeln($result['thoughts']['speak']);
            $io->writeln('');
            $io->writeln('<info>Thoughts:</> ' . $result['thoughts']['text']);
            $io->writeln('<info>Reasoning:</> ' . $result['thoughts']['reasoning']);
            $io->writeln("<info>Plan:</>\n" . $result['thoughts']['plan']);
            $io->writeln('<info>Criticism:</> ' . $result['thoughts']['criticism']);
            $io->writeln('');
            $io->writeln('<fg=blue>Command:</> ' . $result['command']['name']);
            $io->writeln('<fg=blue>Arguments:</> ' . Json::encode($result['command']['args']));
            $io->writeln('');
            $io->writeln('<fg=yellow>Tokens:</> ' . $response->usage->totalTokens . '(prompt ' . $response->usage->promptTokens . '/completion ' . $response->usage->completionTokens . ')');

            if ($result['command']['name'] == 'task_complete') {
                exit;
            }

            $io->confirm('Proceed?', true);

            $commandResult = null;
            $io->writeln('<fg=blue>Executing:</> ' . $result['command']['name'] . ' ' . Json::encode($result['command']['args']));
            try {
                $result = $this->commandRunner->handle(
                    $this->commandBuilder->fromArray($result['command'])
                );
                $commandResult = $result->toJson();
            } catch (\Exception $e) {
                $commandResult = $e->getMessage();
            }
            $io->writeln('<fg=blue>Command result:</> ' . $commandResult);

            $messages[] = ['role' => 'system', 'content' => 'Last command result: ' . $commandResult];
            $messages[] = ['role' => 'user', 'content' => 'Determine which next command to use, and respond using the format specified above:'];
            $step++;
        }
    }
}