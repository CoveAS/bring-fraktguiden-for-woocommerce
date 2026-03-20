<?php
/**
 * Represents a text node in the DOM tree
 */
class BFG_TextNode extends BFG_Node
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getNodeType(): int
    {
        return XML_TEXT_NODE;
    }

    public function getNodeName(): string
    {
        return '#text';
    }
}
