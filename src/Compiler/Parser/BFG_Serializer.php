<?php
/**
 * HTML Serializer
 *
 * Converts a DOM tree back into HTML string.
 * Preserves structure, attributes, and whitespace.
 */
class BFG_Serializer
{
    /**
     * Serialize a node (and its children) to HTML string
     *
     * @param BFG_Node $node Node to serialize
     * @return string HTML string
     */
    public function serialize(BFG_Node $node): string
    {
        if ($node instanceof BFG_ElementNode) {
            return $this->serializeElement($node);
        } elseif ($node instanceof BFG_TextNode) {
            return $node->content;
        } elseif ($node instanceof BFG_CommentNode) {
            return '<!--' . $node->content . '-->';
        } elseif ($node instanceof BFG_PhpNode) {
            return $node->code;
        }

        return '';
    }

    /**
     * Serialize an element node to HTML
     *
     * @param BFG_ElementNode $element Element to serialize
     * @return string HTML string
     */
    private function serializeElement(BFG_ElementNode $element): string
    {
        $html = '<' . $element->tagName;

        // Add attributes
        $attributes = $element->getAttributes();
        foreach ($attributes as $attr) {
            $html .= ' ' . $attr->name;
            if ($attr->value !== '') {
                // Only escape double quotes to prevent breaking the attribute syntax
                // Don't HTML-encode other characters as this is internal processing,
                // not final HTML output. Encoding here causes issues with nested components
                // where attribute values would be double-encoded.
                $escapedValue = str_replace('"', '&quot;', $attr->value);
                $html .= '="' . $escapedValue . '"';
            }
        }

        // Self-closing tag
        if ($element->selfClosing && empty($element->childNodes)) {
            $html .= '/>';
            return $html;
        }

        $html .= '>';

        // Serialize children
        foreach ($element->childNodes as $child) {
            $html .= $this->serialize($child);
        }

        // Closing tag
        $html .= '</' . $element->tagName . '>';

        return $html;
    }

    /**
     * Serialize multiple nodes to HTML
     *
     * @param BFG_Node[] $nodes Nodes to serialize
     * @return string HTML string
     */
    public function serializeNodes(array $nodes): string
    {
        $html = '';
        foreach ($nodes as $node) {
            $html .= $this->serialize($node);
        }
        return $html;
    }
}
