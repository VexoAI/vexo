<?php

declare(strict_types=1);

namespace Vexo\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameters::class)]
final class ParametersTest extends TestCase
{
    public function testToArray(): void
    {
        $values = ['key1' => 'value1', 'key2' => 'value2'];
        $parameters = new Parameters($values);

        $this->assertSame($values, $parameters->toArray());
    }

    public function testWithDefaults(): void
    {
        $values = ['key1' => 'value1', 'key2' => 'value2'];
        $defaultValues = ['key1' => 'default1', 'key3' => 'default3'];

        $parameters = new Parameters($values);
        $newParameters = $parameters->withDefaults($defaultValues);

        $expectedValues = [
            'key1' => 'value1',
            'key3' => 'default3',
            'key2' => 'value2',
        ];

        $this->assertSame($expectedValues, $newParameters->toArray());
    }
}
