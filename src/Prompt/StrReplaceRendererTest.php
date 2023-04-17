<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\TestCase;

final class StrReplaceRendererTest extends TestCase
{
    public function testRender(): void
    {
        $engine = new StrReplaceRenderer();
        $template = 'Hello, {{name}}! Welcome to {{location}}.';
        $values = [
            'name' => 'John',
            'location' => 'Earth'
        ];

        $expectedResult = 'Hello, John! Welcome to Earth.';
        $prompt = $engine->render($template, $values);

        $this->assertSame($expectedResult, $prompt->text());
    }
}
