<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\Chain\PassthroughChain;

#[CoversClass(BasicSingleActionAgent::class)]
final class BasicSingleActionAgentTest extends TestCase
{
    public function testPlan(): void
    {
        $llmChain = new PassthroughChain();
        $actionResolver = new StubActionResolver();
        $agent = new BasicSingleActionAgent($llmChain, $actionResolver);

        $this->assertInstanceOf(Action::class, $agent->plan(new Input(['action' => 'do_something'])));
        $this->assertInstanceOf(Finish::class, $agent->plan(new Input([])));
    }
}

final class StubActionResolver implements ActionResolver
{
    public function formatInstructions(): string
    {
        return '';
    }

    public function parse(Output $output): Action|Finish
    {
        $action = $output->data()['action'] ?? null;

        if ($action === 'do_something') {
            return new Action('do_something', []);
        }

        return new Finish();
    }
}
