<?php
/**
 * Represents an HTML element node
 *
 * Provides DOM-like API for tree manipulation and traversal.
 */
class BFG_ElementNode extends BFG_Node
{
    public string $tagName;
    public bool $selfClosing;

    /** @var array<string, string> Attribute name => value map */
    private array $attributesMap = [];

    /** @var BFG_Node[] Child nodes */
    public array $childNodes = [];

    public function __construct(string $tagName, bool $selfClosing = false)
    {
        $this->tagName = $tagName;
        $this->selfClosing = $selfClosing;
    }

    public function getNodeType(): int
    {
        return XML_ELEMENT_NODE;
    }

    public function getNodeName(): string
    {
        return strtolower($this->tagName);
    }

    /**
     * Set an attribute value
     */
    public function setAttribute(string $name, string $value): void
    {
        $this->attributesMap[$name] = $value;
    }

    /**
     * Get an attribute value
     * @return string|null Returns null if attribute doesn't exist
     */
    public function getAttribute(string $name): ?string
    {
        return $this->attributesMap[$name] ?? null;
    }

    /**
     * Remove an attribute
     */
    public function removeAttribute(string $name): void
    {
        unset($this->attributesMap[$name]);
    }

    /**
     * Get all elements with the specified tag name
     * @param string $tagName Tag name to search for, or '*' for all elements
     * @return BFG_ElementNode[]
     */
    public function getElementsByTagName(string $tagName): array
    {
        $results = [];

        // Check this element
        if ($tagName === '*' || strcasecmp($this->tagName, $tagName) === 0) {
            $results[] = $this;
        }

        // Recursively check children
        foreach ($this->childNodes as $child) {
            if ($child instanceof BFG_ElementNode) {
                $results = array_merge($results, $child->getElementsByTagName($tagName));
            }
        }

        return $results;
    }

    /**
     * Append a child node
     */
    public function appendChild(BFG_Node $node): void
    {
        $node->parentNode = $this;
        $this->childNodes[] = $node;
    }

    /**
     * Insert a node before a reference node
     */
    public function insertBefore(BFG_Node $newNode, BFG_Node $refNode): void
    {
        $index = array_search($refNode, $this->childNodes, true);
        if ($index === false) {
            throw new Exception('Reference node not found');
        }

        $newNode->parentNode = $this;
        array_splice($this->childNodes, $index, 0, [$newNode]);
    }

    /**
     * Remove a child node
     */
    public function removeChild(BFG_Node $node): void
    {
        $index = array_search($node, $this->childNodes, true);
        if ($index === false) {
            throw new Exception('Node not found');
        }

        $node->parentNode = null;
        array_splice($this->childNodes, $index, 1);
    }

    /**
     * Replace a child node with a new node
     */
    public function replaceChild(BFG_Node $newNode, BFG_Node $oldNode): void
    {
        $index = array_search($oldNode, $this->childNodes, true);
        if ($index === false) {
            throw new Exception('Old node not found');
        }

        $oldNode->parentNode = null;
        $newNode->parentNode = $this;
        $this->childNodes[$index] = $newNode;
    }

    /**
     * Get text content (all descendant text concatenated)
     */
    public function getTextContent(): string
    {
        $text = '';

        foreach ($this->childNodes as $child) {
            if ($child instanceof BFG_TextNode) {
                $text .= $child->content;
            } elseif ($child instanceof BFG_ElementNode) {
                $text .= $child->getTextContent();
            }
        }

        return $text;
    }

    /**
     * Set text content (replaces all children with a single text node)
     */
    public function setTextContent(string $text): void
    {
        $this->childNodes = [];
        $textNode = new BFG_TextNode($text);
        $this->appendChild($textNode);
    }

    /**
     * Get first child node
     */
    public function getFirstChild(): ?BFG_Node
    {
        return $this->childNodes[0] ?? null;
    }

    /**
     * Get next sibling node
     */
    public function getNextSibling(): ?BFG_Node
    {
        if (!$this->parentNode) {
            return null;
        }

        $index = array_search($this, $this->parentNode->childNodes, true);
        if ($index === false) {
            return null;
        }

        return $this->parentNode->childNodes[$index + 1] ?? null;
    }

    /**
     * Get attributes as array of BFG_Attribute objects
     * @return BFG_Attribute[]
     */
    public function getAttributes(): array
    {
        $attributes = [];
        foreach ($this->attributesMap as $name => $value) {
            $attributes[] = new BFG_Attribute($name, $value);
        }
        return $attributes;
    }

    /**
     * Magic getter for property access
     */
    public function __get(string $name)
    {
        return match($name) {
            'tagName' => $this->tagName,
            'nodeName' => $this->getNodeName(),
            'nodeType' => $this->getNodeType(),
            'textContent' => $this->getTextContent(),
            'attributes' => $this->getAttributes(),
            'childNodes' => $this->childNodes,
            'parentNode' => $this->parentNode,
            'firstChild' => $this->getFirstChild(),
            'nextSibling' => $this->getNextSibling(),
            default => null,
        };
    }

    /**
     * Magic setter for property access
     */
    public function __set(string $name, $value): void
    {
        if ($name === 'textContent') {
            $this->setTextContent($value);
        }
    }
}
