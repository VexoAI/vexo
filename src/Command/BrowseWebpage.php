<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use Assert\Assertion as Ensure;

final class BrowseWebpage implements Command
{
    public static function fromArray(array $arguments): Command
    {
        Ensure::keyExists($arguments, 'url');
        Ensure::url($arguments['url']);

        Ensure::keyExists($arguments, 'question');
        Ensure::notEmpty($arguments['question']);

        return new BrowseWebpage($arguments['url'], $arguments['question']);
    }

    public function __construct(
        public readonly string $url,
        public readonly string $question
    ) {
    }

    public function arguments(): array
    {
        return ['url' => $this->url, 'question' => $this->question];
    }
}