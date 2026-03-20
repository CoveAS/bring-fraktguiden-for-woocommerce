<?php
/**
 * Base node interface for the BFG custom HTML parser
 *
 * All node types (Element, Text, Comment, PHP) implement this interface.
 */

// Define node type constants for compatibility with Dom\HTMLDocument
if (!defined('XML_ELEMENT_NODE')) {
    define('XML_ELEMENT_NODE', 1);
}
if (!defined('XML_TEXT_NODE')) {
    define('XML_TEXT_NODE', 3);
}
if (!defined('XML_COMMENT_NODE')) {
    define('XML_COMMENT_NODE', 8);
}

abstract class BFG_Node
{
    public ?BFG_ElementNode $parentNode = null;

    /**
     * Get the node type
     * @return int One of XML_ELEMENT_NODE, XML_TEXT_NODE, XML_COMMENT_NODE
     */
    abstract public function getNodeType(): int;

    /**
     * Get the node name
     * @return string
     */
    abstract public function getNodeName(): string;

    /**
     * Magic getter for property access (e.g., $node->nodeType)
     */
    public function __get(string $name)
    {
        return match($name) {
            'nodeType' => $this->getNodeType(),
            'nodeName' => $this->getNodeName(),
            'parentNode' => $this->parentNode,
            default => null,
        };
    }
}
