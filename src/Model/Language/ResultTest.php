<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata;

#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $metadata = new Metadata(['key1' => 'value1', 'key2' => 'value2']);
        $result = new Result(['A great result'], $metadata);

        $this->assertSame(['A great result'], $result->generations());
        $this->assertSame($metadata, $result->metadata());
    }
}
