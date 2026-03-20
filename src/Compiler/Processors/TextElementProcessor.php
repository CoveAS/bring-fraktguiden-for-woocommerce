<?php
/**
 * Processes <t>varname</t> text element replacements in templates
 *
 * Replaces template variables with translatable text or dynamic expressions.
 * For static attributes: <t>title</t> → <?php esc_html_e('value', 'domain'); ?>
 * For dynamic attributes: <t>title</t> → <?php echo ($expression); ?>
 */
class BFG_TextElementProcessor implements BFG_ProcessorInterface
{
    private BFG_PhpCodeGenerator $phpGenerator;
    private BFG_AttributeManager $attrManager;

    public function __construct(BFG_PhpCodeGenerator $phpGenerator, BFG_AttributeManager $attrManager)
    {
        $this->phpGenerator = $phpGenerator;
        $this->attrManager = $attrManager;
    }

    public function process(BFG_Document $doc): void
    {
        $textElements = [];
        foreach ($doc->getElementsByTagName('t') as $element) {
            $textElements[] = $element;
        }

        foreach ($textElements as $element) {
            $varName = trim($element->textContent);
            $value = $this->attrManager->get($varName, '');

            // Track attribute usage
            if ($this->attrManager->has($varName)) {
                $this->attrManager->markAsUsed($varName);
            }

            // Create placeholder comment (will be converted to PHP later)
            if ($this->attrManager->isDynamic($varName)) {
                // Dynamic attribute - output as PHP expression
                $phpCode = $this->phpGenerator->echoExpression($value);
            } else {
                // Static attribute - output as translatable string
                $phpCode = $this->phpGenerator->translatableText($value);
            }
            $comment = $this->phpGenerator->createPlaceholder($doc, $phpCode);

            // Replace <t> element with comment placeholder
            $element->parentNode->replaceChild($comment, $element);
        }
    }
}
