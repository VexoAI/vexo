<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

use PHPUnit\Framework\TestCase;

final class MustacheRenderEngineTest extends TestCase
{
    private MustacheRenderEngine $renderEngine;
    private \Mustache_Engine $mustache;

    protected function setUp(): void
    {
        $this->mustache = new \Mustache_Engine();
        $this->renderEngine = new MustacheRenderEngine($this->mustache);
    }

    public function testRender(): void
    {
        $template = 'Hello, {{name}}!';
        $values = ['name' => 'John'];

        $expectedResult = 'Hello, John!';
        $result = $this->renderEngine->render($template, $values);

        $this->assertSame($expectedResult, $result);
    }
}
