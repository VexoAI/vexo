<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(StrReplaceRenderer::class)]
final class StrReplaceRendererTest extends TestCase
{
    public function testRender(): void
    {
        $renderer = new StrReplaceRenderer('What is the capital of {{country}}?');

        $context = new Context(['country' => 'France']);
        $prompt = $renderer->render($context);

        $this->assertEquals('What is the capital of France?', $prompt);
    }

    public function testRenderReplacesInCorrectOrder(): void
    {
        $renderer = new StrReplaceRenderer('Roses are {{first_color}}, violets are {{second_color}}.');

        $context = new Context(['second_color' => 'Blue', 'first_color' => 'Red']);
        $prompt = $renderer->render($context);

        $this->assertEquals('Roses are Red, violets are Blue.', $prompt);
    }
}
