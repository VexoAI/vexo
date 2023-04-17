<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $generations = [
            new Generation('one'),
            new Generation('two'),
        ];

        $response = new Response($generations);

        $this->assertSame($generations, $response->generations());
    }

    public function testConstructorValidatesGenerations(): void
    {
        $generations = [
            new Generation('one'),
            new Generation('two'),
            'Not a generation'
        ];

        $this->expectException(\InvalidArgumentException::class);
        new Response($generations);
    }
}
