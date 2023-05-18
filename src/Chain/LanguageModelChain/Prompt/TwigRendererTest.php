<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment as Twig;
use Twig\Loader\ArrayLoader;
use Vexo\Chain\Context;

#[CoversClass(TwigRenderer::class)]
final class TwigRendererTest extends TestCase
{
    public function testRender(): void
    {
        $twig = new Twig(
            new ArrayLoader([
                'prompt.twig' => 'What is the capital of {{ country }}?'
            ])
        );
        $renderer = new TwigRenderer($twig, 'prompt.twig');

        $context = new Context(['country' => 'France']);
        $prompt = $renderer->render($context);

        $this->assertSame('What is the capital of France?', $prompt);
    }

    public function testRenderThrowsExceptionOnMissingValues(): void
    {
        $twig = new Twig(
            new ArrayLoader([
                'prompt.twig' => 'What is the capital of {{ country }}?'
            ]),
            ['strict_variables' => true]
        );
        $renderer = new TwigRenderer($twig, 'prompt.twig');

        $this->expectException(FailedToRenderPrompt::class);
        $renderer->render(new Context());
    }

    public function testCreateWithLoader(): void
    {
        $loader = new ArrayLoader([
            'prompt.twig' => 'What is the capital of {{ country }}?'
        ]);

        $renderer = TwigRenderer::createWithLoader('prompt.twig', $loader);

        $context = new Context(['country' => 'France']);
        $prompt = $renderer->render($context);

        $this->assertSame('What is the capital of France?', $prompt);
    }

    public function testCreateWithFilesystemLoader(): void
    {
        $filesystem = vfsStream::setup('templates');
        vfsStream::newFile('prompt.twig')->at($filesystem)->setContent('What is the capital of {{ country }}?');

        $renderer = TwigRenderer::createWithFilesystemLoader('prompt.twig', $filesystem->url());

        $context = new Context(['country' => 'France']);
        $prompt = $renderer->render($context);

        $this->assertSame('What is the capital of France?', $prompt);
    }
}
