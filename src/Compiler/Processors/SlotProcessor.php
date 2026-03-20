<?php
/**
 * Processes <slot/> replacements in templates
 *
 * Replaces <slot/> tags with the actual slot content passed to the component.
 */
class BFG_SlotProcessor implements BFG_ProcessorInterface
{
    private string $slotContent;

    public function __construct(string $slotContent)
    {
        $this->slotContent = $slotContent;
    }

    public function process(BFG_Document $doc): void
    {
        $slotElements = [];
        foreach ($doc->getElementsByTagName('slot') as $element) {
            $slotElements[] = $element;
        }

        foreach ($slotElements as $slotElement) {
            // Wrap slot content in a div to ensure all content (including comments) is preserved
            $wrappedContent = '<div>' . $this->slotContent . '</div>';
            $slotDoc = BFG_Document::createFromString($wrappedContent, LIBXML_NOERROR);

            // Get the wrapper div's children (not the div itself)
            $wrapperDiv = $slotDoc->body->firstChild;
            if ($wrapperDiv && $wrapperDiv->nodeType === XML_ELEMENT_NODE) {
                // Import and insert nodes from inside the wrapper
                foreach ($wrapperDiv->childNodes as $child) {
                    $imported = $doc->importNode($child, true);
                    $slotElement->parentNode->insertBefore($imported, $slotElement);
                }
            }

            // Remove slot element
            $slotElement->parentNode->removeChild($slotElement);
        }
    }
}
