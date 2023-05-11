<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader\TextSplitter;

use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;

trait SplitDocumentsBehavior
{
    public function splitDocuments(DocumentsContract $documents): DocumentsContract
    {
        $splitDocuments = new Documents();
        foreach ($documents as $document) {
            $splits = $this->split($document->contents());
            foreach ($splits as $split) {
                $splitDocuments->add(
                    new Document($split, $document->metadata())
                );
            }
        }

        return $splitDocuments;
    }
}
