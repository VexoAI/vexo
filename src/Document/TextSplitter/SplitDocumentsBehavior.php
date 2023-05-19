<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter;

use Vexo\Document\Document;
use Vexo\Document\Documents;

trait SplitDocumentsBehavior
{
    public function splitDocuments(Documents $documents): Documents
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
