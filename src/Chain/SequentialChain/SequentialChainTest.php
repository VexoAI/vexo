<?php

declare(strict_types=1);

namespace Vexo\Chain\SequentialChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Chains;
use Vexo\Chain\FailedToValidateInput;
use Vexo\Chain\Input;
use Vexo\Chain\PassthroughChain\PassthroughChain;

#[CoversClass(SequentialChain::class)]
final class SequentialChainTest extends TestCase
{
    public function testProcess(): void
    {
        $sequentialChain = new SequentialChain(
            new Chains([new PassthroughChain(), new PassthroughChain()]),
            inputKeys: ['foo', 'fudge'],
            outputKeys: ['foo']
        );

        $input = new Input(['foo' => 'bar', 'fudge' => 'cake']);
        $output = $sequentialChain->process($input);

        $this->assertSame(['foo' => 'bar'], $output->toArray());
    }

    public function testProcessWithOutputAll(): void
    {
        $sequentialChain = new SequentialChain(
            new Chains([new PassthroughChain(), new PassthroughChain()]),
            inputKeys: ['foo', 'fudge'],
            outputAll: true
        );

        $input = new Input(['foo' => 'bar', 'fudge' => 'cake']);
        $output = $sequentialChain->process($input);

        $this->assertSame(['foo' => 'bar', 'fudge' => 'cake'], $output->toArray());
    }

    public function testInputKeys(): void
    {
        $sequentialChain = new SequentialChain(
            chains: new Chains([new PassthroughChain(), new PassthroughChain()]),
            inputKeys: ['fudge'],
            outputKeys: ['fudge']
        );

        $this->assertSame(['fudge'], $sequentialChain->inputKeys());
    }

    public function testUsesAllAvailableVariablesAsOutputKeys(): void
    {
        $sequentialChain = new SequentialChain(
            chains: new Chains([new PassthroughChain(outputKeys: ['foo']), new PassthroughChain(outputKeys: ['bar'])]),
            inputKeys: ['baz'],
            outputAll: true
        );

        $this->assertSame(['baz', 'foo', 'bar'], $sequentialChain->outputKeys());
    }

    public function testUsesLastChainOutputKeysAsOutputKeys(): void
    {
        $sequentialChain = new SequentialChain(
            chains: new Chains([new PassthroughChain(outputKeys: ['foo']), new PassthroughChain(outputKeys: ['bar'])])
        );

        $this->assertSame(['bar'], $sequentialChain->outputKeys());
    }

    public function testValidatesMissingInputVariables(): void
    {
        $this->expectException(FailedToValidateInput::class);
        $this->expectExceptionMessageMatches(
            '/Chain .* has input variables that are not known: foo, only had fudge/'
        );

        new SequentialChain(
            chains: new Chains([new PassthroughChain(inputKeys: ['foo']), new PassthroughChain()]),
            inputKeys: ['fudge']
        );
    }

    public function testValidatesOverlappingOutputVariables(): void
    {
        $this->expectException(FailedToValidateInput::class);
        $this->expectExceptionMessageMatches(
            '/Chain .* has output variables that would override known variables: fudge/'
        );

        new SequentialChain(
            chains: new Chains([new PassthroughChain(outputKeys: ['fudge']), new PassthroughChain()]),
            inputKeys: ['fudge']
        );
    }

    public function testValidatesOutputVariablesAreProducedBySequence(): void
    {
        $this->expectException(FailedToValidateInput::class);
        $this->expectExceptionMessage('Output variables are not produced by this sequence: bar');

        new SequentialChain(
            chains: new Chains([new PassthroughChain(), new PassthroughChain()]),
            inputKeys: ['foo'],
            outputKeys: ['bar']
        );
    }
}
