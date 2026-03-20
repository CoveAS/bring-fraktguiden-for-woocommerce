<?php
/**
 * Attribute Parser
 *
 * Parses HTML attribute strings into name-value pairs.
 * Handles quoted values, boolean attributes, and dynamic attributes.
 */
class BFG_AttributeParser
{
    /**
     * Parse an attribute string into an associative array
     *
     * @param string $attributeString Raw attribute string (e.g., 'id="test" class="foo" disabled')
     * @return array<string, string> Array of attribute name => value pairs
     */
    public function parse(string $attributeString): array
    {
        $attributes = [];

        if (trim($attributeString) === '') {
            return $attributes;
        }

        // Pattern to match attributes:
        // - Attribute name: [a-zA-Z:][a-zA-Z0-9:_-]*
        // - Optional value with:
        //   - Double quotes: ="value"
        //   - Single quotes: ='value'
        //   - Unquoted: =value
        //   - No value (boolean): just the name
        $pattern = '/([a-zA-Z:][a-zA-Z0-9:_-]*)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?/';

        if (preg_match_all($pattern, $attributeString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $name = $match[1];

                // Determine value based on which capture group matched
                if (isset($match[2]) && $match[2] !== '') {
                    // Double-quoted value
                    $value = $match[2];
                } elseif (isset($match[3]) && $match[3] !== '') {
                    // Single-quoted value
                    $value = $match[3];
                } elseif (isset($match[4]) && $match[4] !== '') {
                    // Unquoted value
                    $value = $match[4];
                } else {
                    // Boolean attribute (no value)
                    $value = '';
                }

                $attributes[$name] = $value;
            }
        }

        return $attributes;
    }
}
