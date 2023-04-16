<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Tests\Chain\Middleware;

use PHPUnit\Framework\TestCase;
use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Middleware\PassthroughMiddleware;
use Pragmatist\Assistant\Chain\SimpleInput;
use Pragmatist\Assistant\Chain\SimpleOutput;

final class PassthroughMiddlewareTest extends TestCase
{
    public function testPassthroughMiddleware(): void
    {
        $nextCallable = function (Input $input) {
            return new SimpleOutput($input->data());
        };

        $passthroughMiddleware = new PassthroughMiddleware();
        $input = new SimpleInput(['foo' => 'bar']);
        $output = $passthroughMiddleware->process($input, $nextCallable);

        $this->assertSame($input->data(), $output->data());
    }
}
