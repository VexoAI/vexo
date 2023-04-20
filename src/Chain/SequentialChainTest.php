<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SequentialChain::class)]
final class SequentialChainTest extends TestCase
{
    public function testProcess(): void
    {
        $chainOne = new PassthroughChain();
        $chainTwo = new PassthroughChain();
        $sequentialChain = new SequentialChain($chainOne, $chainTwo);

        $input = new Input(['foo' => 'bar']);
        $output = $sequentialChain->process($input);

        $this->assertSame($input->data(), $output->data());
    }

    public function testConstructorValidatesChainCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new SequentialChain();
    }
}
