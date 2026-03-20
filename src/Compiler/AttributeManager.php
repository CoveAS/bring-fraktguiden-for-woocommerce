<?php
/**
 * Manages attribute state during template compilation
 *
 * Tracks which attributes are used, which are dynamic (prefixed with :),
 * and handles dynamic attribute placeholder generation.
 */
class BFG_AttributeManager
{
    private array $attributes = [];
    private array $usedAttributes = [];
    private array $dynamicAttributeNames = [];
    private array $dynamicAttributePlaceholders = [];

    public function __construct(array $attributes = [], array $dynamicAttributeNames = [])
    {
        $this->attributes = $attributes;
        $this->dynamicAttributeNames = $dynamicAttributeNames;
    }

    /**
     * Mark an attribute as used
     */
    public function markAsUsed(string $name): void
    {
        if (!in_array($name, $this->usedAttributes)) {
            $this->usedAttributes[] = $name;
        }
    }

    /**
     * Check if an attribute is dynamic
     */
    public function isDynamic(string $name): bool
    {
        return in_array($name, $this->dynamicAttributeNames);
    }

    /**
     * Get an attribute value
     */
    public function get(string $name, $default = '')
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Check if an attribute exists
     */
    public function has(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Get all attributes
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Get all used attribute names
     */
    public function getUsed(): array
    {
        return $this->usedAttributes;
    }

    /**
     * Get unused attributes
     */
    public function getUnused(): array
    {
        $unused = [];
        foreach ($this->attributes as $name => $value) {
            if (!in_array($name, $this->usedAttributes)) {
                $unused[$name] = $value;
            }
        }
        return $unused;
    }

    /**
     * Get dynamic attribute names
     */
    public function getDynamicNames(): array
    {
        return $this->dynamicAttributeNames;
    }

    /**
     * Create a placeholder for a dynamic attribute and store the expression
     */
    public function createDynamicPlaceholder(string $expression): string
    {
        $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributePlaceholders) . '_ATTR';
        $this->dynamicAttributePlaceholders[$placeholder] = $expression;
        return $placeholder;
    }

    /**
     * Get all dynamic attribute placeholders
     */
    public function getDynamicPlaceholders(): array
    {
        return $this->dynamicAttributePlaceholders;
    }

    /**
     * Reset used attributes tracking
     */
    public function resetUsed(): void
    {
        $this->usedAttributes = [];
    }

    /**
     * Save current used attributes state
     */
    public function saveUsedState(): array
    {
        return $this->usedAttributes;
    }

    /**
     * Restore used attributes state
     */
    public function restoreUsedState(array $state): void
    {
        $this->usedAttributes = $state;
    }
}
