<?php
/**
 * Interface for template processors
 *
 * Each processor handles a specific type of transformation
 * during template compilation.
 */
interface BFG_ProcessorInterface
{
    /**
     * Process a document
     *
     * @param BFG_Document $doc The document to process
     * @return void
     */
    public function process(BFG_Document $doc): void;
}
