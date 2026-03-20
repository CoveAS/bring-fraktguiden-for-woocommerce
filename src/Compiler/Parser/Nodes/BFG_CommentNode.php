<?php
/**
 * Represents an HTML comment node
 */
class BFG_CommentNode extends BFG_Node
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getNodeType(): int
    {
        return XML_COMMENT_NODE;
    }

    public function getNodeName(): string
    {
        return '#comment';
    }
}
