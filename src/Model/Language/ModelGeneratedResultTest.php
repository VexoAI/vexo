<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata;

#[CoversClass(ModelGeneratedResult::class)]
final class ModelGeneratedResultTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $result = new Result(['Paris'], new Metadata(['foo' => 'bar']));
        $event = new ModelGeneratedResult(
            'What is the capital of France?',
            ['Observation:'],
            $result
        );

        $this->assertSame('What is the capital of France?', $event->prompt());
        $this->assertSame(['Observation:'], $event->stops());
        $this->assertSame($result, $event->result());
    }
}
