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
    public function testSequentialPipelineWithMiddleware(): void
    {
        $inputFactory = new SimpleInputFactory();
        $chainOne = new PassthroughChain($inputFactory);
        $chainTwo = new PassthroughChain($inputFactory);

        $middleware = new PassthroughMiddleware();

        $middlewares = [$middleware];
        $sequentialPipeline = new SequentialPipeline([$chainOne, $chainTwo], $middlewares);

        $input = new SimpleInput(['foo' => 'bar']);
        $output = $sequentialPipeline->process($input);

        $this->assertSame($input->data(), $output->data());
    }
}
