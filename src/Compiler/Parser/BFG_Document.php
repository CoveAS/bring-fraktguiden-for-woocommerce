<?php
/**
 * BFG Document - Custom HTML/Template Parser
 *
 * Drop-in replacement for Dom\HTMLDocument with permissive parsing.
 * Preserves ALL tags including custom tags like <slot>, <if>, <else>, <t>.
 *
 * Usage:
 *   $doc = BFG_Document::createFromString($html);
 *   $elements = $doc->getElementsByTagName('slot');
 *   $html = $doc->saveHTML();
 */

// Load dependencies
require_once __DIR__ . '/Nodes/BFG_Node.php';
require_once __DIR__ . '/Nodes/BFG_Attribute.php';
require_once __DIR__ . '/Nodes/BFG_ElementNode.php';
require_once __DIR__ . '/Nodes/BFG_TextNode.php';
require_once __DIR__ . '/Nodes/BFG_CommentNode.php';
require_once __DIR__ . '/Nodes/BFG_PhpNode.php';
require_once __DIR__ . '/BFG_Tokenizer.php';
require_once __DIR__ . '/BFG_AttributeParser.php';
require_once __DIR__ . '/BFG_TreeBuilder.php';
require_once __DIR__ . '/BFG_Serializer.php';

class BFG_Document
{
    public BFG_ElementNode $body;
    private BFG_Tokenizer $tokenizer;
    private BFG_TreeBuilder $treeBuilder;
    private BFG_Serializer $serializer;

    private function __construct()
    {
        $this->tokenizer = new BFG_Tokenizer();
        $this->treeBuilder = new BFG_TreeBuilder();
        $this->serializer = new BFG_Serializer();
    }

    /**
     * Create a document from an HTML string
     *
     * @param string $html HTML content to parse
     * @param int $options Parser options (for compatibility, currently ignored)
     * @return BFG_Document
     */
    public static function createFromString(string $html, int $options = 0): BFG_Document
    {
        $doc = new self();

        // Tokenize HTML
        $tokens = $doc->tokenizer->tokenize($html);

        // Build DOM tree
        $doc->body = $doc->treeBuilder->build($tokens);

        return $doc;
    }

    /**
     * Get all elements with the specified tag name
     *
     * @param string $tagName Tag name to search for, or '*' for all elements
     * @return BFG_ElementNode[]
     */
    public function getElementsByTagName(string $tagName): array
    {
        // Don't include the body element itself in wildcard search
        $results = [];

        foreach ($this->body->childNodes as $child) {
            if ($child instanceof BFG_ElementNode) {
                $results = array_merge($results, $child->getElementsByTagName($tagName));
            }
        }

        return $results;
    }

    /**
     * Create a comment node
     *
     * @param string $text Comment text (without <!-- -->)
     * @return BFG_CommentNode
     */
    public function createComment(string $text): BFG_CommentNode
    {
        return new BFG_CommentNode($text);
    }

    /**
     * Import a node from another document
     *
     * @param BFG_Node $node Node to import
     * @param bool $deep If true, recursively clone all descendants
     * @return BFG_Node Cloned node
     */
    public function importNode(BFG_Node $node, bool $deep = false): BFG_Node
    {
        if ($node instanceof BFG_ElementNode) {
            $clone = new BFG_ElementNode($node->tagName, $node->selfClosing);

            // Copy attributes
            foreach ($node->getAttributes() as $attr) {
                $clone->setAttribute($attr->name, $attr->value);
            }

            // Deep clone children
            if ($deep) {
                foreach ($node->childNodes as $child) {
                    $clonedChild = $this->importNode($child, true);
                    $clone->appendChild($clonedChild);
                }
            }

            return $clone;
        } elseif ($node instanceof BFG_TextNode) {
            return new BFG_TextNode($node->content);
        } elseif ($node instanceof BFG_CommentNode) {
            return new BFG_CommentNode($node->content);
        } elseif ($node instanceof BFG_PhpNode) {
            return new BFG_PhpNode($node->code);
        }

        throw new Exception('Unknown node type');
    }

    /**
     * Serialize the document (or a specific node) to HTML
     *
     * @param BFG_Node|null $node Node to serialize (null = entire document)
     * @return string HTML string
     */
    public function saveHTML(?BFG_Node $node = null): string
    {
        if ($node === null) {
            // Serialize entire document body
            return $this->serializer->serializeNodes($this->body->childNodes);
        }

        // Serialize specific node
        return $this->serializer->serialize($node);
    }

    /**
     * Magic getter for property access (e.g., $doc->body)
     */
    public function __get(string $name)
    {
        if ($name === 'body') {
            return $this->body;
        }
        return null;
    }
}
