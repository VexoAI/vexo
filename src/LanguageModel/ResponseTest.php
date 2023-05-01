<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $completions = Completions::fromString("one\n\ntwo");

        $metadata = new ResponseMetadata(['key1' => 'value1', 'key2' => 'value2']);

        $response = new Response($completions, $metadata);

        $this->assertSame($completions, $response->completions());
        $this->assertSame($metadata, $response->metadata());
    }

    public function testFromString(): void
    {
        $response = Response::fromString("one\n\ntwo");

        $this->assertEquals(Completions::fromString("one\n\ntwo"), $response->completions());
    }
}
