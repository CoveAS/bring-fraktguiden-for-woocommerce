<?php
/**
 * Generates PHP code placeholders for template compilation
 *
 * Creates placeholder comments that will be converted to actual
 * PHP code in the final compilation step.
 */
class BFG_PhpCodeGenerator
{
    private string $textDomain;

    public function __construct(string $textDomain = 'bring-fraktguiden-for-woocommerce')
    {
        $this->textDomain = $textDomain;
    }

    /**
     * Create a PHP placeholder comment node
     */
    public function createPlaceholder(Dom\HTMLDocument $doc, string $phpCode): Dom\Comment
    {
        return $doc->createComment("BFG_PHP:{$phpCode}");
    }

    /**
     * Generate translatable text code
     */
    public function translatableText(string $text): string
    {
        $escapedText = addslashes(trim($text));
        return "esc_html_e('{$escapedText}', '{$this->textDomain}');";
    }

    /**
     * Generate echo expression code
     */
    public function echoExpression(string $expression): string
    {
        return "echo ({$expression});";
    }

    /**
     * Generate conditional start code
     */
    public function conditionalStart(string $expression): string
    {
        return "if (!empty({$expression})): ";
    }

    /**
     * Generate else code
     */
    public function conditionalElse(): string
    {
        return "else: ";
    }

    /**
     * Generate conditional end code
     */
    public function conditionalEnd(): string
    {
        return "endif; ";
    }
}
