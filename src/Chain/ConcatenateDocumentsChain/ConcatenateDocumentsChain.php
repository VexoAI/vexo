<?php

declare(strict_types=1);

namespace Vexo\Chain\ConcatenateDocumentsChain;

use Vexo\Chain\Attribute\RequiresContextValue;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Contract\Document\Documents;

final class ConcatenateDocumentsChain implements Chain
{
    #[RequiresContextValue('documents', Documents::class)]
    public function run(Context $context): void
    {
        /** @var Documents $documents */
        $documents = $context->get('documents');

        $combinedContents = implode(
            "\n\n",
            array_map(fn ($document): string => $document->contents(), $documents->toArray())
        );

        $context->put('combined_contents', $combinedContents);
    }
}
