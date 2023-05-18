<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Vexo\Chain\Context;

final class TwigRenderer implements Renderer
{
    public static function createWithFilesystemLoader(string $template, string $path = __DIR__ . '/templates'): self
    {
        return static::createWithLoader($template, new FilesystemLoader($path));
    }

    /**
     * @param array<string, mixed> $twigOptions
     */
    public static function createWithLoader(string $template, LoaderInterface $loader, array $twigOptions = []): self
    {
        return new self(
            new Twig($loader, ['autoescape' => false, ...$twigOptions]),
            $template
        );
    }

    public function __construct(
        private readonly Twig $twig,
        private readonly string $template
    ) {
        $this->twig->enableStrictVariables();
    }

    public function render(Context $context): string
    {
        $template = $this->twig->load($this->template);

        try {
            return $template->render($context->toArray());
        } catch (\Throwable $exception) {
            throw FailedToRenderPrompt::because($exception);
        }
    }
}
