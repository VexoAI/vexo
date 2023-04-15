<?php

declare(strict_types=1);

namespace Pragmatist\Assistant;

use GuzzleHttp\Exception\TransferException;
use OpenAI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RunCommand extends Command
{
    const PROMPT = 'You are Assistant-AI, the helpful personal
Your decisions must always be made independently without seeking user assistance. Play to your strengths as an LLM and pursue simple strategies with no legal complications.

GOALS:
1. Find out what the best restaurant is in Amsterdam

Constraints:
1. ~4000 word limit for short term memory. Your short term memory is short, so immediately save important information to files.
2. If you are unsure how you previously did something or want to recall past events, thinking about similar events will help you remember.
3. No user assistance
4. Exclusively use the commands listed in double quotes e.g. "command name"

Commands:
1. Google Search: "google", args: "input": "<search>"
2. Browse Webpage: "browse_webpage", args: "url": "<url>", "question": "<question_you_want_answered_by_the_webpage>"
3. Write to file: "write_to_file", args: "file": "<file>", "text": "<text>"
4. Read file: "read_file", args: "file": "<file>"
5. Task Complete (Shutdown): "task_complete", args: "reason": "<reason>"
6. Do Nothing: "do_nothing", args:

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

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Runs the assistant');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = OpenAI::client($_ENV['OPENAI_API_KEY']);
        $httpClient = new \GuzzleHttp\Client(['timeout' => 10]);
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

            $response = $client->chat()->create(['model' => 'gpt-3.5-turbo', 'messages' => $messages]);

            $result = json_decode($response->choices[0]->message->content, true);
            file_put_contents(__DIR__ . '/../output/llm-response-' . time() . '.txt', $response->choices[0]->message->content);

            $messages[] = ['role' => 'assistant', 'content' => json_encode($result)];

            $io->writeln($result['thoughts']['speak']);
            $io->writeln('');
            $io->writeln('<info>Thoughts:</> ' . $result['thoughts']['text']);
            $io->writeln('<info>Reasoning:</> ' . $result['thoughts']['reasoning']);
            $io->writeln("<info>Plan:</>\n" . $result['thoughts']['plan']);
            $io->writeln('<info>Criticism:</> ' . $result['thoughts']['criticism']);
            $io->writeln('');
            $io->writeln('<fg=blue>Command:</> ' . $result['command']['name']);
            $io->writeln('<fg=blue>Arguments:</> ' . json_encode($result['command']['args']));
            $io->writeln('');
            $io->writeln('<fg=yellow>Tokens:</> ' . $response->usage->totalTokens . '(prompt ' . $response->usage->promptTokens . '/completion ' . $response->usage->completionTokens . ')');

            $io->confirm('Proceed?', true);

            $commandResult = null;
            $io->writeln('<fg=blue>Executing:</> ' . $result['command']['name'] . ' ' . json_encode($result['command']['args']));
            switch ($result['command']['name']) {
                case 'google':
                    $apiResponse = $httpClient->request(
                        'GET',
                        sprintf(
                            'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s',
                            $_ENV['GOOGLE_API_KEY'],
                            $_ENV['GOOGLE_CUSTOM_SEARCH_ENGINE_ID'],
                            $result['command']['args']['input']
                        )
                    );
                    $jsonResult = json_decode((string) $apiResponse->getBody(), true);
                    $searchResults = [];
                    foreach (array_slice($jsonResult['items'], 0, 3) as $item) {
                        $searchResults[] = ['title' => $item['title'], 'link' => $item['link']];
                    }
                    $commandResult = json_encode($searchResults);
                    break;
                case 'browse_webpage':
                    try {
                        $io->writeln('<fg=blue>Downloading page:</> ' . $result['command']['args']['url']);
                        $browseResponse = $httpClient->get($result['command']['args']['url']);
                    } catch (TransferException $e) {
                        $commandResult = 'An error occurred: ' . $e->getMessage();
                        break;
                    }

                    //$browseResponse = file_get_contents(__DIR__ . '/../accuweather_source.html');
                    $browseResponse = preg_replace('#<head(.*?)>(.*?)</head>#is', '', (string) $browseResponse->getBody());
                    $browseResponse = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $browseResponse);
                    $browseResponse = strip_tags($browseResponse);
                    $browseResponse = preg_replace('#\s+#', ' ', $browseResponse);

                    $io->write('<fg=blue>Analyzing text:</> ');
                    $browseLlmResponse = $client->chat()->create(
                        [
                            'model' => 'gpt-3.5-turbo',
                            'messages' => [
                                ['role' => 'user', 'content' => '"""' . $browseResponse . '"""'],
                                ['role' => 'user', 'content' => 'Using the above text, please answer the following question: "' . $result['command']['args']['question'] . '"'],
                                ['role' => 'user', 'content' => 'Do not provide other information besides answering the question.'],
                                ['role' => 'user', 'content' => 'If the question cannot be answered, explicitly say so and then summarize the text.']
                            ]
                        ]
                    );
                    $io->writeln('done.');
                    $commandResult = $browseLlmResponse->choices[0]->message->content;
                    break;
                case 'write_to_file':
                    file_put_contents(__DIR__ . '/../output/' . $result['command']['args']['file'], $result['command']['args']['text']);
                    $commandResult = 'File written successfully';
                    break;
                case 'read_file':
                    $commandResult = file_get_contents(__DIR__ . '/../output/' . $result['command']['args']['file']);
                    break;
                case 'task_complete':
                    exit;
            }
            $io->writeln('<fg=blue>Command result:</> ' . $commandResult);

            $messages[] = ['role' => 'system', 'content' => 'Last command result: ' . $commandResult];
            $messages[] = ['role' => 'user', 'content' => 'Determine which next command to use, and respond using the format specified above:'];
            $step++;
        }

        return self::SUCCESS;
    }
}