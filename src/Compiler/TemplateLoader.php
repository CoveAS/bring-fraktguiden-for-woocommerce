<?php
/**
 * Loads component template files (.bfgc.php)
 *
 * Responsible for reading component templates and stripping
 * the PHP documentation header blocks.
 */
class BFG_TemplateLoader
{
    private string $componentsDir;

    public function __construct(string $componentsDir)
    {
        $this->componentsDir = $componentsDir;
    }

    /**
     * Load a component template file
     */
    public function load(string $componentName): string
    {
        $templatePath = $this->componentsDir . '/' . $componentName . '.bfgc.php';

        if (!file_exists($templatePath)) {
            throw new Exception("Component template not found: {$componentName}.bfgc.php\nSearched in: {$this->componentsDir}");
        }

        $content = file_get_contents($templatePath);

        // Remove PHP blocks from the beginning
        // Look for the pattern: */ followed by newline and ? > which indicates end of doc comment
        $phpOpen = '<' . '?php';
        $phpClose = '?' . '>';

        if (str_starts_with(trim($content), $phpOpen)) {
            // Find closing tag that's on its own line (end of PHP block, not in comment examples)
            $pattern = '/\*\/\s*\n' . preg_quote($phpClose, '/') . '\s*\n/';
            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                // Skip past the entire match
                $content = substr($content, $matches[0][1] + strlen($matches[0][0]));
            } else {
                // Fallback: just find ? > at end of line
                $pattern = '/' . preg_quote($phpClose, '/') . '\s*$/m';
                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $content = substr($content, $matches[0][1] + strlen($matches[0][0]));
                }
            }
        }

        return trim($content);
    }
}
