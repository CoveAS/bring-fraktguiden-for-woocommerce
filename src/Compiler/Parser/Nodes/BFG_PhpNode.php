<?php
/**
 * Represents a PHP code block
 *
 * Preserved as-is, no evaluation. Includes the <?php ?> tags.
 */
class BFG_PhpNode extends BFG_Node
{
    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getNodeType(): int
    {
        return XML_COMMENT_NODE; // Treated similar to comments for traversal
    }

    public function getNodeName(): string
    {
        return '#php';
    }
}
