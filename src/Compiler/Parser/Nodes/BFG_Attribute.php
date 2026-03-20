<?php
/**
 * Represents an HTML attribute with name and value
 *
 * Lightweight class used when iterating element attributes.
 */
class BFG_Attribute
{
    public string $name;
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}
