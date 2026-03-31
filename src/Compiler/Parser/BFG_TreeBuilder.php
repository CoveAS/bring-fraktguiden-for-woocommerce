<?php
/**
 * Tree Builder
 *
 * Builds a DOM tree from a stream of tokens.
 * Uses a stack-based approach to track nesting.
 */
class BFG_TreeBuilder
{
    private BFG_AttributeParser $attributeParser;

    public function __construct()
    {
        $this->attributeParser = new BFG_AttributeParser();
    }

    /**
     * Check if a tag name represents a custom component
     *
     * Custom components contain hyphens or dots (e.g., "bfg-section", "bfg-field.text")
     *
     * @param string $tagName
     * @return bool
     */
    private function isCustomComponent(string $tagName): bool
    {
        return strpos($tagName, '-') !== false || strpos($tagName, '.') !== false;
    }

    /**
     * Build a DOM tree from tokens
     *
     * @param array $tokens Token array from BFG_Tokenizer
     * @return BFG_ElementNode Root element (html or body wrapper)
     */
    public function build(array $tokens): BFG_ElementNode
    {
        // Create a root wrapper (we'll return its body element)
        $root = new BFG_ElementNode('html');
        $body = new BFG_ElementNode('body');
        $root->appendChild($body);

        // Stack to track open elements
        $stack = [$body];

        foreach ($tokens as $token) {
            $currentParent = end($stack);

            switch ($token['type']) {
                case 'opening_tag':
                    $element = new BFG_ElementNode($token['tag_name'], false);

                    // Parse and set attributes
                    if (!empty($token['attributes'])) {
                        $attributes = $this->attributeParser->parse($token['attributes']);
                        foreach ($attributes as $name => $value) {
                            $element->setAttribute($name, $value);
                        }
                    }

                    $currentParent->appendChild($element);
                    $stack[] = $element; // Push to stack (expecting closing tag)
                    break;

                case 'self_closing_tag':
                    // Validate: custom components cannot be self-closing
                    if ($this->isCustomComponent($token['tag_name'])) {
                        throw new Exception(
                            "Self-closing custom components are not allowed. " .
                            "Found self-closing tag: <{$token['tag_name']} />. " .
                            "Custom components must have an opening and closing tag."
                        );
                    }

                    $element = new BFG_ElementNode($token['tag_name'], true);

                    // Parse and set attributes
                    if (!empty($token['attributes'])) {
                        $attributes = $this->attributeParser->parse($token['attributes']);
                        foreach ($attributes as $name => $value) {
                            $element->setAttribute($name, $value);
                        }
                    }

                    $currentParent->appendChild($element);
                    // Don't push to stack (self-closing)
                    break;

                case 'closing_tag':
                    // Pop from stack if tag names match
                    if (count($stack) > 1) {
                        $lastElement = end($stack);
                        if ($lastElement instanceof BFG_ElementNode) {
                            // Check if tag names match (case-insensitive)
                            if (strcasecmp($lastElement->tagName, $token['tag_name']) === 0) {
                                array_pop($stack);
                            } else {
                                // Mismatched closing tag - try to find matching opening tag in stack
                                $foundMatch = false;
                                for ($i = count($stack) - 1; $i >= 1; $i--) {
                                    if ($stack[$i] instanceof BFG_ElementNode &&
                                        strcasecmp($stack[$i]->tagName, $token['tag_name']) === 0) {
                                        // Found match - pop all elements up to and including this one
                                        array_splice($stack, $i);
                                        $foundMatch = true;
                                        break;
                                    }
                                }
                                // If no match found, ignore the closing tag (orphaned)
                            }
                        }
                    }
                    break;

                case 'text':
                    $textNode = new BFG_TextNode($token['content']);
                    $currentParent->appendChild($textNode);
                    break;

                case 'comment':
                    // Extract comment content (without <!-- -->)
                    $content = $token['content'];
                    if (preg_match('/^<!--(.*)-->$/s', $content, $matches)) {
                        $content = $matches[1];
                    }
                    $commentNode = new BFG_CommentNode($content);
                    $currentParent->appendChild($commentNode);
                    break;

                case 'php':
                    $phpNode = new BFG_PhpNode($token['content']);
                    $currentParent->appendChild($phpNode);
                    break;
            }
        }

        return $body;
    }
}
