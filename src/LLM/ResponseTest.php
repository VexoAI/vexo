<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $generations = new Generations(
            new Generation('one'),
            new Generation('two'),
        );

        $metadata = new ResponseMetadata(['key1' => 'value1', 'key2' => 'value2']);

        $response = new Response($generations, $metadata);

        $this->assertSame($generations, $response->generations());
        $this->assertSame($metadata, $response->metadata());
    }
}
