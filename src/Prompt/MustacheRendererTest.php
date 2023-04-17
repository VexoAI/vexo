<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\TestCase;

final class MustacheRendererTest extends TestCase
{
    private MustacheRenderer $renderer;
    private \Mustache_Engine $mustache;

    protected function setUp(): void
    {
        $this->mustache = new \Mustache_Engine();
        $this->renderer = new MustacheRenderer($this->mustache);
    }

    public function testRender(): void
    {
        $template = 'Hello, {{name}}!';
        $values = ['name' => 'John'];

        $expectedResult = 'Hello, John!';
        $prompt = $this->renderer->render($template, $values);

        $this->assertSame($expectedResult, $prompt->text());
    }
}
