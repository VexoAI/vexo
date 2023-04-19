<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\TestCase;

final class ResponseMetadataTest extends TestCase
{
    public function testToArray(): void
    {
        $values = ['key1' => 'value1', 'key2' => 'value2'];
        $modelResult = new ResponseMetadata($values);

        $this->assertSame($values, $modelResult->toArray());
    }
}
