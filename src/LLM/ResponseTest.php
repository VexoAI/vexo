<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $generations = Generations::fromString("one\n\ntwo");

        $metadata = new ResponseMetadata(['key1' => 'value1', 'key2' => 'value2']);

        $response = new Response($generations, $metadata);

        $this->assertSame($generations, $response->generations());
        $this->assertSame($metadata, $response->metadata());
    }

    public function testFromString(): void
    {
        $response = Response::fromString("one\n\ntwo");

        $this->assertEquals(Generations::fromString("one\n\ntwo"), $response->generations());
    }
}
