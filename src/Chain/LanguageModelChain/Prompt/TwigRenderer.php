<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;
use Vexo\Chain\Context;

final class TwigRenderer implements Renderer
{
    public static function createWithFilesystemLoader(string $template, string $path = __DIR__ . '/templates'): self
    {
        return new self(new Twig(new FilesystemLoader($path)), $template);
    }

    public function __construct(
        private readonly Twig $twig,
        private readonly string $template
    ) {
    }

    public function render(Context $context): string
    {
        $template = $this->twig->load($this->template);

        return $template->render($context->toArray());
    }
}
