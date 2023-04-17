<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Tests\Chain;

use PHPUnit\Framework\TestCase;
use Pragmatist\Assistant\Chain\Middleware\PassthroughMiddleware;
use Pragmatist\Assistant\Chain\PassthroughChain;
use Pragmatist\Assistant\Chain\SimpleInput;
use Pragmatist\Assistant\Chain\SimpleInputFactory;
use Pragmatist\Assistant\Chain\SequentialPipeline;

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