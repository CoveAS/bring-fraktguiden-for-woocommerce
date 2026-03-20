<?php
/**
 * Processes <if :varname>...<else>...</else></if> conditionals
 *
 * For static conditionals: evaluates at compile time
 * For dynamic conditionals: generates runtime PHP if/else/endif
 */
class BFG_ConditionalProcessor implements BFG_ProcessorInterface
{
    private BFG_PhpCodeGenerator $phpGenerator;
    private BFG_AttributeManager $attrManager;

    public function __construct(BFG_PhpCodeGenerator $phpGenerator, BFG_AttributeManager $attrManager)
    {
        $this->phpGenerator = $phpGenerator;
        $this->attrManager = $attrManager;
    }

    public function process(Dom\HTMLDocument $doc): void
    {
        $ifElements = [];
        foreach ($doc->getElementsByTagName('if') as $element) {
            $ifElements[] = $element;
        }

        foreach ($ifElements as $ifElement) {
            // Find the condition variable from :varname attribute
            $condition = null;
            foreach ($ifElement->attributes as $attr) {
                if (str_starts_with($attr->name, ':')) {
                    $condition = substr($attr->name, 1); // Remove ':'
                    break;
                }
            }

            if (!$condition) {
                continue;
            }

            // Track attribute usage
            $this->attrManager->markAsUsed($condition);

            if ($this->attrManager->isDynamic($condition)) {
                $this->processDynamicConditional($doc, $ifElement, $condition);
            } else {
                $this->processStaticConditional($ifElement, $condition);
            }
        }
    }

    private function processDynamicConditional(Dom\HTMLDocument $doc, Dom\Element $ifElement, string $condition): void
    {
        // Dynamic conditional - generate runtime PHP if/else
        $conditionExpression = $this->attrManager->get($condition);

        // Find <else> sibling if it exists
        $elseElement = $this->findElseSibling($ifElement);

        // Create PHP conditional start
        if (!$ifElement->parentNode) {
            return;
        }

        $phpCode = $this->phpGenerator->conditionalStart($conditionExpression);
        $phpIfStart = $this->phpGenerator->createPlaceholder($doc, $phpCode);
        $ifElement->parentNode->insertBefore($phpIfStart, $ifElement);

        // Move if content directly (no serialization)
        $this->moveChildrenBefore($ifElement, $ifElement);

        if ($elseElement) {
            // Add else clause
            $phpElse = $this->phpGenerator->createPlaceholder($doc, $this->phpGenerator->conditionalElse());
            $ifElement->parentNode->insertBefore($phpElse, $ifElement);

            // Move else content directly (no serialization)
            $this->moveChildrenBefore($elseElement, $ifElement);

            if ($elseElement->parentNode) {
                $elseElement->parentNode->removeChild($elseElement);
            }
        }

        // Add endif
        $phpEndif = $this->phpGenerator->createPlaceholder($doc, $this->phpGenerator->conditionalEnd());
        $ifElement->parentNode->insertBefore($phpEndif, $ifElement);

        $ifElement->parentNode->removeChild($ifElement);
    }

    private function processStaticConditional(Dom\Element $ifElement, string $condition): void
    {
        // Static conditional - evaluate at compile time
        $conditionMet = !empty($this->attrManager->get($condition));

        // Find <else> sibling if it exists
        $elseElement = $this->findElseSibling($ifElement);

        if ($conditionMet) {
            // Condition true: keep <if> content, remove <else>
            $this->moveChildrenBefore($ifElement, $ifElement);
            if ($elseElement && $elseElement->parentNode) {
                $elseElement->parentNode->removeChild($elseElement);
            }
        } else {
            // Condition false: keep <else> content if exists
            if ($elseElement) {
                $this->moveChildrenBefore($elseElement, $ifElement);
                if ($elseElement->parentNode) {
                    $elseElement->parentNode->removeChild($elseElement);
                }
            }
        }

        // Remove <if> element
        if ($ifElement->parentNode) {
            $ifElement->parentNode->removeChild($ifElement);
        }
    }

    private function findElseSibling(Dom\Element $element): ?Dom\Element
    {
        $nextSibling = $element->nextSibling;
        while ($nextSibling) {
            if ($nextSibling->nodeType === XML_ELEMENT_NODE && strtolower($nextSibling->nodeName) === 'else') {
                return $nextSibling;
            }
            $nextSibling = $nextSibling->nextSibling;
        }
        return null;
    }

    private function moveChildrenBefore(Dom\Element $source, Dom\Element $before): void
    {
        $children = [];
        foreach ($source->childNodes as $child) {
            $children[] = $child;
        }
        if ($source->parentNode) {
            foreach ($children as $child) {
                $source->parentNode->insertBefore($child, $before);
            }
        }
    }
}
