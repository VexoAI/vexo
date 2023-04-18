<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline\Middleware;

use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;

final class PassthroughMiddlewareTest extends TestCase
{
    public function testPassthroughMiddleware(): void
    {
        $nextCallable = function (Input $input) {
            return new Output($input->data());
        };

        $passthroughMiddleware = new PassthroughMiddleware();
        $input = new Input(['foo' => 'bar']);
        $output = $passthroughMiddleware->process($input, $nextCallable);

        $this->assertSame($input->data(), $output->data());
    }
}
