<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use Vexo\Chain\Attribute\RequiresContextValue;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Retriever\Retriever;

final class DocumentsRetrieverChain implements Chain
{
    public function __construct(
        private readonly Retriever $retriever
    ) {
    }

    #[RequiresContextValue('query', 'string')]
    public function run(Context $context): void
    {
        $context->put(
            'documents',
            $this->retriever->retrieve($context->get('query'))
        );
    }
}
