<?php

declare(strict_types=1);

namespace Vexo\Chain\PassthroughChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Input;

#[CoversClass(PassthroughChain::class)]
final class PassthroughChainTest extends TestCase
{
    public function testProcess(): void
    {
        $passthroughChain = new PassthroughChain();

        $input = new Input(['foo' => 'bar']);
        $output = $passthroughChain->process($input);

        $this->assertSame(['foo' => 'bar'], $output->toArray());
    }

    public function testInputKeys(): void
    {
        $passthroughChain = new PassthroughChain(inputKeys: ['fudge']);

        $this->assertSame(['fudge'], $passthroughChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $passthroughChain = new PassthroughChain(outputKeys: ['fudge']);

        $this->assertSame(['fudge'], $passthroughChain->outputKeys());
    }
}
