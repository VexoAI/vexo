<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BasicPromptTemplate::class)]
final class BasicPromptTemplateTest extends TestCase
{
    public function testRender(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'What is the capital of {{country}}?',
            ['country']
        );

        $this->assertEquals(
            'What is the capital of France?',
            $promptTemplate->render(['country' => 'France'])
        );
    }

    public function testRenderValidatesValues(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'What is the capital of {{country}}?',
            ['country']
        );

        $this->expectException(SorryNotAllRequiredValuesWereGiven::class);
        $promptTemplate->render(['foo' => 'bar']);
    }
}
