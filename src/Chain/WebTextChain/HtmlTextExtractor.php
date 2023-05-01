<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

final class HtmlTextExtractor implements TextExtractor
{
    /**
     * @var string[]
     */
    private const TAGS_TO_REMOVE = ['head', 'script', 'style', 'noscript', 'iframe', 'meta', 'link'];

    public function extract(string $contents): string
    {
        if (trim($contents) === '') {
            return '';
        }

        // Remove any tags that generally don't contain useful content
        $text = $this->removeTags($contents);

        // Replace any newlines surrounded by whitespace with a single newline
        $textWithNewlinesReduced = preg_replace("/\s*\n\s*/", "\n", $text);

        // Replace any other multiple whitespace characters by a single space
        return trim(preg_replace("/[^\S\n]+/", ' ', $textWithNewlinesReduced));
    }

    private function removeTags(string $contents): string
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($contents, 'HTML-ENTITIES', 'UTF-8'), \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);

        // Remove the specified tags and their content
        $xpath = new \DOMXPath($dom);
        foreach (self::TAGS_TO_REMOVE as $tag) {
            $nodes = $xpath->query('//' . $tag);
            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        // Return only the text content left
        return $dom->textContent;
    }
}
