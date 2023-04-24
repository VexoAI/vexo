<?php

declare(strict_types=1);

namespace Vexo\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResponseMetadata::class)]
final class ResponseMetadataTest extends TestCase
{
    public function testToArray(): void
    {
        $values = ['key1' => 'value1', 'key2' => 'value2'];
        $modelResult = new ResponseMetadata($values);

        $this->assertSame($values, $modelResult->toArray());
    }
}
