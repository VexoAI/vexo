<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline;

use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\PassthroughChain;
use Vexo\Weave\Chain\SimpleInput;
use Vexo\Weave\Chain\SimpleInputFactory;
use Vexo\Weave\Pipeline\Middleware\PassthroughMiddleware;

final class SequentialPipelineTest extends TestCase
{
    public function testSequentialPipelineConstructorValidatesChains(): void
    {
        $inputFactory = new SimpleInputFactory();
        $chainOne = new PassthroughChain($inputFactory);
        $chainTwo = new PassthroughChain($inputFactory);

        $middleware = new PassthroughMiddleware();

        // Valid chains array
        $sequentialPipeline = new SequentialPipeline([$chainOne, $chainTwo]);
        $sequentialPipeline->addMiddleware($middleware);

        // Invalid chains array
        $this->expectException(\InvalidArgumentException::class);
        new SequentialPipeline([$chainOne, $chainTwo, 'InvalidChain']);
    }

    public function testSequentialPipelineWithMiddleware(): void
    {
        $inputFactory = new SimpleInputFactory();
        $chainOne = new PassthroughChain($inputFactory);
        $chainTwo = new PassthroughChain($inputFactory);

        $middleware = new PassthroughMiddleware();

        $sequentialPipeline = new SequentialPipeline([$chainOne, $chainTwo]);
        $sequentialPipeline->addMiddleware($middleware);

        $input = new SimpleInput(['foo' => 'bar']);
        $output = $sequentialPipeline->process($input);

        $this->assertSame($input->data(), $output->data());
    }
}