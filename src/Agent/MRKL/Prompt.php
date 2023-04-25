<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

final class Prompt
{
    public const PREFIX = <<<PREFIX
        Answer the following questions as best you can. You have access to the following tools:
        PREFIX;

    public const FORMAT_INSTRUCTIONS = <<<INSTRUCTIONS
        Use the following format:

        Question: the input question you must answer
        Thought: you should always think about what to do
        Action: the action to take, should be one of [{{tool_names}}]
        Action Input: the input to the action
        Observation: the result of the action
        ... (this Thought/Action/Action Input/Observation can repeat N times)
        Thought: I now know the final answer
        Final Answer: the final answer to the original input question
        INSTRUCTIONS;

    public const SUFFIX = <<<SUFFIX
        Begin!

        Question: {{question}}
        Thought: {{scratchpad}}
        SUFFIX;
}
