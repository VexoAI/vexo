<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline\Concerns;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\Pipeline\Middleware\PassthroughMiddleware;

#[CoversClass(SupportsMiddleware::class)]
final class SupportsMiddlewareTest extends TestCase
{
    public function testProcessWithMiddleware(): void
    {
        $middleware = new PassthroughMiddleware();
        $supportsMiddleware = new SupportsMiddlewareSUT();
        $supportsMiddleware->addMiddleware($middleware);

        $input = new Input(['Some input']);
        $output = $supportsMiddleware->process($input, fn (Input $input) => new Output($input->data()));

        $this->assertSame($input->data(), $output->data());
    }

    public function testWithoutMiddleware(): void
    {
        $supportsMiddleware = new SupportsMiddlewareSUT();

        $input = new Input(['Some input']);
        $output = $supportsMiddleware->process($input, fn (Input $input) => new Output($input->data()));

        $this->assertSame($input->data(), $output->data());
    }
}

final class SupportsMiddlewareSUT
{
    use SupportsMiddleware;
    use SupportsLogging;

    public function process(Input $input, callable $corePipeline): Output
    {
        return $this->processWithMiddlewares($input, $corePipeline);
    }
}
