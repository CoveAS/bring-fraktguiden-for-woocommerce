<?php
/**
 * Processes :varname attribute replacements in templates
 *
 * Replaces attribute values that start with : with actual values.
 * For static attributes: href=":url" → href="https://example.com"
 * For dynamic attributes: href=":url" → href="<?php echo $url; ?>" (via placeholder)
 */
class BFG_AttributeProcessor implements BFG_ProcessorInterface
{
    private BFG_AttributeManager $attrManager;
    private array $dynamicAttributePlaceholders;

    public function __construct(BFG_AttributeManager $attrManager, array &$dynamicAttributePlaceholders)
    {
        $this->attrManager = $attrManager;
        $this->dynamicAttributePlaceholders = &$dynamicAttributePlaceholders;
    }

    public function process(BFG_Document $doc): void
    {
        foreach ($doc->getElementsByTagName('*') as $element) {
            foreach ($element->attributes as $attr) {
                if (str_starts_with($attr->value, ':')) {
                    $varName = substr($attr->value, 1); // Remove ':'
                    $value = $this->attrManager->get($varName, '');

                    // Track attribute usage
                    if ($this->attrManager->has($varName)) {
                        $this->attrManager->markAsUsed($varName);
                    }

                    if ($this->attrManager->isDynamic($varName)) {
                        // Dynamic attribute - create placeholder for PHP expression
                        $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributePlaceholders) . '_ATTR';
                        $this->dynamicAttributePlaceholders[$placeholder] = $value;
                        $element->setAttribute($attr->name, $placeholder);
                    } else {
                        // Static attribute - use value directly
                        $element->setAttribute($attr->name, $value);
                    }
                }
            }
        }
    }
}
