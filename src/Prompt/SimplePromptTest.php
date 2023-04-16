<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

use PHPUnit\Framework\TestCase;

final class SimplePromptTest extends TestCase
{
    public function testRender(): void
    {
        $engine = new StrReplaceRenderEngine();
        $template = 'Hello, {{name}}!';
        $values = ['name' => 'John'];

        $simplePrompt = new SimplePrompt($engine, $template);

        $this->assertEquals('Hello, John!', $simplePrompt->render($values));
    }

    public function testTemplate(): void
    {
        $engine = new StrReplaceRenderEngine();
        $template = 'test_template';

        $simplePrompt = new SimplePrompt($engine, $template);

        $this->assertEquals($template, $simplePrompt->template());
    }
}