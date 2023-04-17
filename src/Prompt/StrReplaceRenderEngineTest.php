<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

use PHPUnit\Framework\TestCase;

final class StrReplaceRenderEngineTest extends TestCase
{
    public function testRender(): void
    {
        $engine = new StrReplaceRenderEngine();
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
