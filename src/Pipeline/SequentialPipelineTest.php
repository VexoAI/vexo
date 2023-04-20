<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\PassthroughChain;

#[CoversClass(SequentialPipeline::class)]
final class SequentialPipelineTest extends TestCase
{
    public function testSequentialPipelineConstructorValidatesChains(): void
    {
        $chainOne = new PassthroughChain();
        $chainTwo = new PassthroughChain();

        $this->expectException(\InvalidArgumentException::class);
        new SequentialPipeline([$chainOne, $chainTwo, 'InvalidChain']);
    }

    public function testSequentialPipelineProcess(): void
    {
        $chainOne = new PassthroughChain();
        $chainTwo = new PassthroughChain();
        $sequentialPipeline = new SequentialPipeline([$chainOne, $chainTwo]);

        $input = new Input(['foo' => 'bar']);
        $output = $sequentialPipeline->process($input);

        $this->assertSame($input->data(), $output->data());
    }
}
