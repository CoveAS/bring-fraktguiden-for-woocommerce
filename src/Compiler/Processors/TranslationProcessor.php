<?php
/**
 * Processes standalone <t> tags for translation
 *
 * Converts <t>Text</t> to <?php esc_html_e('Text', 'text-domain'); ?>
 * Only processes <t> tags that are NOT direct children of <bfg-*> components
 */
class BFG_TranslationProcessor implements BFG_ProcessorInterface
{
    private BFG_PhpCodeGenerator $phpGenerator;

    public function __construct(BFG_PhpCodeGenerator $phpGenerator)
    {
        $this->phpGenerator = $phpGenerator;
    }

    public function process(Dom\HTMLDocument $doc): void
    {
        $translationTags = [];

        // Collect all <t> tags (not <bfg-t>, just plain <t>)
        foreach ($doc->getElementsByTagName('t') as $element) {
            // Skip <t> tags that are DIRECT children of <bfg-*> components
            // (those are for the component's own text processing)
            // But process <t> tags in regular HTML elements inside components
            // (those are user content that needs translation)
            $directParent = $element->parentNode;
            $isDirectChildOfComponent = $directParent
                && $directParent->nodeType === XML_ELEMENT_NODE
                && str_starts_with(strtolower($directParent->tagName), 'bfg-');

            if (!$isDirectChildOfComponent) {
                $translationTags[] = $element;
            }
        }

        foreach ($translationTags as $tElement) {
            $textContent = trim($tElement->textContent);

            // Create placeholder comment (will be converted to PHP later)
            $phpCode = $this->phpGenerator->translatableText($textContent);
            $comment = $this->phpGenerator->createPlaceholder($doc, $phpCode);

            // Replace <t> element with comment placeholder
            $tElement->parentNode->replaceChild($comment, $tElement);
        }
    }
}
