<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;

#[CoversClass(BaseChain::class)]
#[IgnoreClassForCodeCoverage(ChainStarted::class)]
#[IgnoreClassForCodeCoverage(ChainFinished::class)]
final class BaseChainTest extends TestCase
{
    public function testProcess(): void
    {
        $baseChain = new StubBaseChain();

        $input = new Input(['foo' => 'bar']);
        $output = $baseChain->process($input);

        $this->assertSame(['foo' => 'bar'], $output->data());
    }

    public function testProcessValidatesInput(): void
    {
        $baseChain = new StubBaseChain();

        $this->expectException(SorryValidationFailed::class);
        $baseChain->process(new Input(['invalid' => 'input']));
    }
}

final class StubBaseChain extends BaseChain
{
    public function inputKeys(): array
    {
        return ['foo'];
    }

    public function outputKeys(): array
    {
        return ['foo'];
    }

    public function call(Input $input): Output
    {
        return new Output($input->data());
    }
}
