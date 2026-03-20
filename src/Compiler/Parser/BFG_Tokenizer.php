<?php
/**
 * HTML/Template Tokenizer
 *
 * Converts HTML string into a stream of tokens for parsing.
 * Handles custom tags, PHP blocks, and standard HTML.
 */
class BFG_Tokenizer
{
    /**
     * Tokenize HTML string into structured tokens
     *
     * @param string $html HTML content to tokenize
     * @return array Array of tokens with 'type' and 'content' keys
     */
    public function tokenize(string $html): array
    {
        $tokens = [];
        $position = 0;
        $length = strlen($html);

        while ($position < $length) {
            // Check for PHP blocks (highest priority - preserve as-is)
            if (substr($html, $position, 5) === '<?php') {
                $endPos = strpos($html, '?>', $position);
                if ($endPos === false) {
                    // Unclosed PHP tag - consume rest of document
                    $endPos = $length - 2;
                }
                $code = substr($html, $position, $endPos + 2 - $position);
                $tokens[] = ['type' => 'php', 'content' => $code];
                $position = $endPos + 2;
                continue;
            }

            // Check for HTML comments
            if (substr($html, $position, 4) === '<!--') {
                $endPos = strpos($html, '-->', $position);
                if ($endPos === false) {
                    // Unclosed comment - consume rest of document
                    $endPos = $length - 3;
                }
                $comment = substr($html, $position, $endPos + 3 - $position);
                $tokens[] = ['type' => 'comment', 'content' => $comment];
                $position = $endPos + 3;
                continue;
            }

            // Check for closing tag
            if (substr($html, $position, 2) === '</') {
                if (preg_match('/<\/([a-zA-Z][a-zA-Z0-9:.-]*)\s*>/', $html, $matches, 0, $position)) {
                    $tokens[] = [
                        'type' => 'closing_tag',
                        'tag_name' => $matches[1]
                    ];
                    $position += strlen($matches[0]);
                    continue;
                }
            }

            // Check for opening tag or self-closing tag
            if ($html[$position] === '<') {
                // Match opening tag with optional attributes and self-closing slash
                // Pattern handles quoted attribute values properly (including > inside quotes)
                // Ensures / is only matched as self-closing indicator, not as part of attributes
                if (preg_match('/<([a-zA-Z][a-zA-Z0-9:.-]*)(\s+(?:[^"\'\/>]|"[^"]*"|\'[^\']*\')*)?\s*(\/)?>/s', $html, $matches, 0, $position)) {
                    $tagName = $matches[1];
                    $attributesString = isset($matches[2]) ? trim($matches[2]) : '';
                    $selfClosing = isset($matches[3]) && $matches[3] === '/';

                    $tokens[] = [
                        'type' => $selfClosing ? 'self_closing_tag' : 'opening_tag',
                        'tag_name' => $tagName,
                        'attributes' => $attributesString
                    ];
                    $position += strlen($matches[0]);
                    continue;
                }
            }

            // Find next tag or special sequence
            $nextTagPos = $this->findNextTag($html, $position);

            if ($nextTagPos === false || $nextTagPos > $position) {
                // Extract text content until next tag (or end of string)
                $textEnd = $nextTagPos === false ? $length : $nextTagPos;
                $text = substr($html, $position, $textEnd - $position);

                // Only add non-empty text
                if ($text !== '') {
                    $tokens[] = ['type' => 'text', 'content' => $text];
                }
                $position = $textEnd;
            } else {
                // Shouldn't reach here, but advance to avoid infinite loop
                $position++;
            }
        }

        return $tokens;
    }

    /**
     * Find the position of the next tag or special sequence
     *
     * @param string $html HTML string
     * @param int $position Starting position
     * @return int|false Position of next tag, or false if none found
     */
    private function findNextTag(string $html, int $position): int|false
    {
        $nextTag = strpos($html, '<', $position);
        return $nextTag;
    }
}
