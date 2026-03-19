#!/usr/bin/env php
<?php
/**
 * BFG Component Compiler
 *
 * Compiles .bfg.php source files into standard PHP templates
 * using .bfgc.php component templates.
 *
 * Usage:
 *   php bin/compile-templates.php                    # Compile all templates
 *   php bin/compile-templates.php path/to/file.bfg.php  # Single file
 */

class BFGComponentCompiler
{
    private string $projectRoot;
    private string $componentsDir;
    private string $textDomain = 'bring-fraktguiden-for-woocommerce';
    private array $usedAttributes = [];
    private array $dynamicAttributes = [];

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->componentsDir = $projectRoot . '/src/components';
    }

    /**
     * Compile a .bfg.php source file to standard PHP
     */
    public function compile(string $sourceFilePath): string
    {
        if (!file_exists($sourceFilePath)) {
            throw new Exception("Source file not found: {$sourceFilePath}");
        }

        $sourceContent = file_get_contents($sourceFilePath);

        // Protect existing PHP tags before HTML parsing
        $phpTagMap = [];
        $sourceContent = preg_replace_callback(
            '/<\?php(.*?)\?>/s',
            function($matches) use (&$phpTagMap) {
                $placeholder = '<!--BFG_PHP_PRESERVE_' . count($phpTagMap) . '-->';
                $phpTagMap[$placeholder] = '<?php' . $matches[1] . '?>';
                return $placeholder;
            },
            $sourceContent
        );

        // Parse source file as HTML
        $sourceDoc = Dom\HTMLDocument::createFromString($sourceContent, LIBXML_NOERROR);

        // Process standalone <t> tags for translation (before component processing)
        $this->processTranslationTags($sourceDoc);

        // Find all <bfg-*> component tags and process them
        $this->processComponentTags($sourceDoc);

        // Extract only the body content (avoid <!DOCTYPE>, <html>, etc.)
        $output = '';
        foreach ($sourceDoc->body->childNodes as $node) {
            $output .= $sourceDoc->saveHTML($node);
        }

        // Convert compiler-generated placeholder comments back to PHP tags
        $output = preg_replace('/<!--BFG_PHP:(.*?)-->/', '<?php $1 ?>', $output);

        // Replace dynamic attribute placeholders with PHP code
        foreach ($this->dynamicAttributes as $placeholder => $expression) {
            $output = str_replace($placeholder, '<?php echo ' . $expression . '; ?>', $output);
        }

        // Restore original PHP tags
        foreach ($phpTagMap as $placeholder => $phpTag) {
            $output = str_replace($placeholder, $phpTag, $output);
        }

        return $output;
    }

    /**
     * Process standalone <t> tags for translation
     * Converts <t>Text</t> to <?php esc_html_e('Text', 'text-domain'); ?>
     * Only processes <t> tags that are NOT inside <bfg-*> components
     */
    private function processTranslationTags(Dom\HTMLDocument $doc): void
    {
        $translationTags = [];

        // Collect all <t> tags (not <bfg-t>, just plain <t>)
        foreach ($doc->getElementsByTagName('t') as $element) {
            // Check if this <t> is inside a <bfg-*> component
            $isInsideComponent = false;
            $parent = $element->parentNode;
            while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
                if (str_starts_with(strtolower($parent->tagName), 'bfg-')) {
                    $isInsideComponent = true;
                    break;
                }
                $parent = $parent->parentNode;
            }

            // Only process if not inside a component
            if (!$isInsideComponent) {
                $translationTags[] = $element;
            }
        }

        foreach ($translationTags as $tElement) {
            $textContent = trim($tElement->textContent);

            // Create placeholder comment (will be converted to PHP later)
            $escapedText = addslashes($textContent);
            $phpCode = "esc_html_e('{$escapedText}', '{$this->textDomain}');";
            $comment = $doc->createComment("BFG_PHP:{$phpCode}");

            // Replace <t> element with comment placeholder
            $tElement->parentNode->replaceChild($comment, $tElement);
        }
    }

    /**
     * Process all <bfg-*> component tags in the document
     */
    private function processComponentTags(Dom\HTMLDocument $doc): void
    {
        $componentTags = [];

        // Collect all elements that start with 'bfg-' (case-insensitive)
        foreach ($doc->getElementsByTagName('*') as $element) {
            $tagName = strtolower($element->tagName);
            if (str_starts_with($tagName, 'bfg-')) {
                $componentTags[] = $element;
            }
        }

        // Process each component tag
        foreach ($componentTags as $tag) {
            $this->processComponentTag($tag, $doc);
        }
    }

    /**
     * Process a single <bfg-*> component tag
     */
    private function processComponentTag(Dom\Element $tag, Dom\HTMLDocument $sourceDoc): void
    {
        $componentName = substr(strtolower($tag->tagName), 4); // Remove 'bfg-' prefix

        // Extract attributes (including dynamic ones with : prefix)
        $attributes = [];
        $dynamicAttrs = [];
        foreach ($tag->attributes as $attr) {
            if (str_starts_with($attr->name, ':')) {
                // Dynamic attribute - store separately
                $dynamicAttrs[substr($attr->name, 1)] = $attr->value;
            } else {
                $attributes[$attr->name] = $attr->value;
            }
        }

        // Extract slot content (inner HTML or text content for <bfg-t>)
        $slotContent = '';
        if ($componentName === 't') {
            // For <bfg-t>, use text content only (for translation)
            $slotContent = $tag->textContent;
        } else {
            // For other components, use full HTML
            foreach ($tag->childNodes as $child) {
                $slotContent .= $sourceDoc->saveHTML($child);
            }
        }

        // Load component template
        $templateContent = $this->loadComponentTemplate($componentName);
        $templateDoc = Dom\HTMLDocument::createFromString($templateContent, LIBXML_NOERROR);

        // Reset used attributes tracking
        $this->usedAttributes = [];

        // Merge dynamic attributes into regular attributes, but track which ones are dynamic
        $dynamicAttrNames = array_keys($dynamicAttrs);
        $attributes = array_merge($attributes, $dynamicAttrs);

        // Always pass slot content as 'slot' attribute so templates can use <t>slot</t>
        // For <bfg-t>, use text content only; for others, use full HTML
        if ($componentName === 't') {
            $attributes['slot'] = $slotContent;
        } else {
            // Make slot content available as attribute for translatable slots
            // Strip HTML tags to get plain text for translation
            $attributes['slot'] = strip_tags($slotContent);
        }

        // Mark 'slot' as used so it doesn't get passed through as an attribute
        $this->usedAttributes[] = 'slot';

        // Apply replacements in order
        $this->replaceTextElements($templateDoc, $attributes, $dynamicAttrNames);
        $this->replaceAttributeVariables($templateDoc, $attributes, $dynamicAttrNames);
        $this->replaceConditionals($templateDoc, $attributes, $dynamicAttrNames);
        $this->replaceSlots($templateDoc, $slotContent);

        // Save used attributes before processing nested components
        $savedUsedAttributes = $this->usedAttributes;

        // Process any nested component tags that were introduced by slot content
        $this->processComponentTags($templateDoc);

        // Restore used attributes after nested component processing
        $this->usedAttributes = $savedUsedAttributes;

        // Clean up any remaining <else> tags (they should have been removed by conditional processing)
        $this->cleanupElseTags($templateDoc);

        // Get root element and apply unmatched attributes
        $rootElement = $templateDoc->body->firstChild;
        while ($rootElement && $rootElement->nodeType !== XML_ELEMENT_NODE) {
            $rootElement = $rootElement->nextSibling;
        }

        if ($rootElement instanceof Dom\Element) {
            $this->applyUnmatchedAttributes($rootElement, $attributes, $this->usedAttributes, $dynamicAttrNames);
        }

        // Import compiled template into source document
        $compiledNodes = [];
        foreach ($templateDoc->body->childNodes as $node) {
            $imported = $sourceDoc->importNode($node, true);
            $compiledNodes[] = $imported;
        }

        // Replace original tag with compiled nodes
        $parent = $tag->parentNode;
        foreach ($compiledNodes as $node) {
            $parent->insertBefore($node, $tag);
        }
        $parent->removeChild($tag);
    }

    /**
     * Load a component template file
     */
    private function loadComponentTemplate(string $componentName): string
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

    /**
     * Replace <t>varname</t> with <?php esc_html_e('value', 'text-domain'); ?>
     * For dynamic attributes, output <?php echo expression; ?>
     */
    private function replaceTextElements(Dom\HTMLDocument $doc, array $attributes, array $dynamicAttrNames = []): void
    {
        $textElements = [];
        foreach ($doc->getElementsByTagName('t') as $element) {
            $textElements[] = $element;
        }

        foreach ($textElements as $element) {
            $varName = trim($element->textContent);
            $value = $attributes[$varName] ?? '';

            // Track attribute usage
            if (isset($attributes[$varName])) {
                $this->usedAttributes[] = $varName;
            }

            // Create placeholder comment (will be converted to PHP later)
            if (in_array($varName, $dynamicAttrNames)) {
                // Dynamic attribute - output as PHP expression
                $phpCode = "echo ({$value});";
            } else {
                // Static attribute - output as translatable string
                $escapedValue = addslashes(trim($value));
                $phpCode = "esc_html_e('{$escapedValue}', '{$this->textDomain}');";
            }
            $comment = $doc->createComment("BFG_PHP:{$phpCode}");

            // Replace <t> element with comment placeholder
            $element->parentNode->replaceChild($comment, $element);
        }
    }

    /**
     * Replace :varname in attributes with actual values
     * For dynamic attributes, create a placeholder that will be replaced with PHP
     */
    private function replaceAttributeVariables(Dom\HTMLDocument $doc, array $attributes, array $dynamicAttrNames = []): void
    {
        foreach ($doc->getElementsByTagName('*') as $element) {
            foreach ($element->attributes as $attr) {
                if (str_starts_with($attr->value, ':')) {
                    $varName = substr($attr->value, 1); // Remove ':'
                    $value = $attributes[$varName] ?? '';

                    // Track attribute usage
                    if (isset($attributes[$varName])) {
                        $this->usedAttributes[] = $varName;
                    }

                    if (in_array($varName, $dynamicAttrNames)) {
                        // Dynamic attribute - create placeholder for PHP expression
                        $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributes) . '_ATTR';
                        $this->dynamicAttributes[$placeholder] = $value;
                        $element->setAttribute($attr->name, $placeholder);
                    } else {
                        // Static attribute - use value directly
                        $element->setAttribute($attr->name, $value);
                    }
                }
            }
        }
    }

    /**
     * Process <if :varname>...<else>...</else></if> conditionals
     * For dynamic attributes, generate runtime PHP conditionals
     */
    private function replaceConditionals(Dom\HTMLDocument $doc, array $attributes, array $dynamicAttrNames = []): void
    {
        $ifElements = [];
        foreach ($doc->getElementsByTagName('if') as $element) {
            $ifElements[] = $element;
        }

        foreach ($ifElements as $ifElement) {
            // Find the condition variable from :varname attribute
            $condition = null;
            foreach ($ifElement->attributes as $attr) {
                if (str_starts_with($attr->name, ':')) {
                    $condition = substr($attr->name, 1); // Remove ':'
                    break;
                }
            }

            if (!$condition) {
                continue;
            }

            // Track attribute usage
            $this->usedAttributes[] = $condition;

            if (in_array($condition, $dynamicAttrNames)) {
                // Dynamic conditional - generate runtime PHP if/else
                $conditionExpression = $attributes[$condition];

                // Find <else> sibling if it exists
                $elseElement = null;
                $nextSibling = $ifElement->nextSibling;
                while ($nextSibling) {
                    if ($nextSibling->nodeType === XML_ELEMENT_NODE && strtolower($nextSibling->nodeName) === 'else') {
                        $elseElement = $nextSibling;
                        break;
                    }
                    $nextSibling = $nextSibling->nextSibling;
                }

                // Create PHP conditional start
                if (!$ifElement->parentNode) {
                    continue;
                }

                $phpCode = "if (!empty({$conditionExpression})): ";
                $phpIfStart = $doc->createComment("BFG_PHP:{$phpCode}");
                $ifElement->parentNode->insertBefore($phpIfStart, $ifElement);

                // Move if content directly (no serialization)
                $ifChildren = [];
                foreach ($ifElement->childNodes as $child) {
                    $ifChildren[] = $child;
                }
                foreach ($ifChildren as $child) {
                    $ifElement->parentNode->insertBefore($child, $ifElement);
                }

                if ($elseElement) {
                    // Add else clause
                    $phpElse = $doc->createComment("BFG_PHP:else: ");
                    $ifElement->parentNode->insertBefore($phpElse, $ifElement);

                    // Move else content directly (no serialization)
                    $elseChildren = [];
                    foreach ($elseElement->childNodes as $child) {
                        $elseChildren[] = $child;
                    }
                    foreach ($elseChildren as $child) {
                        $ifElement->parentNode->insertBefore($child, $ifElement);
                    }

                    if ($elseElement->parentNode) {
                        $elseElement->parentNode->removeChild($elseElement);
                    }
                }

                // Add endif
                $phpEndif = $doc->createComment("BFG_PHP:endif; ");
                $ifElement->parentNode->insertBefore($phpEndif, $ifElement);

                $ifElement->parentNode->removeChild($ifElement);
            } else {
                // Static conditional - evaluate at compile time
                $conditionMet = !empty($attributes[$condition]);

                // Find <else> sibling if it exists
                $elseElement = null;
                $nextSibling = $ifElement->nextSibling;
                while ($nextSibling) {
                    if ($nextSibling->nodeType === XML_ELEMENT_NODE && strtolower($nextSibling->nodeName) === 'else') {
                        $elseElement = $nextSibling;
                        break;
                    }
                    $nextSibling = $nextSibling->nextSibling;
                }

                if ($conditionMet) {
                    // Condition true: keep <if> content, remove <else>
                    $children = [];
                    foreach ($ifElement->childNodes as $child) {
                        $children[] = $child;
                    }
                    if ($ifElement->parentNode) {
                        foreach ($children as $child) {
                            $ifElement->parentNode->insertBefore($child, $ifElement);
                        }
                    }
                    if ($elseElement && $elseElement->parentNode) {
                        $elseElement->parentNode->removeChild($elseElement);
                    }
                } else {
                    // Condition false: keep <else> content if exists
                    if ($elseElement) {
                        $children = [];
                        foreach ($elseElement->childNodes as $child) {
                            $children[] = $child;
                        }
                        if ($ifElement->parentNode) {
                            foreach ($children as $child) {
                                $ifElement->parentNode->insertBefore($child, $ifElement);
                            }
                        }
                        if ($elseElement->parentNode) {
                            $elseElement->parentNode->removeChild($elseElement);
                        }
                    }
                }

                // Remove <if> element
                if ($ifElement->parentNode) {
                    $ifElement->parentNode->removeChild($ifElement);
                }
            }
        }
    }

    /**
     * Clean up any remaining <else> tags (safety cleanup)
     */
    private function cleanupElseTags(Dom\HTMLDocument $doc): void
    {
        $elseTags = [];
        foreach ($doc->getElementsByTagName('else') as $element) {
            $elseTags[] = $element;
        }

        foreach ($elseTags as $elseTag) {
            // This shouldn't happen, but if there are any <else> tags left, remove them
            // but keep their children
            $children = [];
            foreach ($elseTag->childNodes as $child) {
                $children[] = $child;
            }
            foreach ($children as $child) {
                $elseTag->parentNode->insertBefore($child, $elseTag);
            }
            $elseTag->parentNode->removeChild($elseTag);
        }
    }

    /**
     * Replace <slot/> with actual content
     */
    private function replaceSlots(Dom\HTMLDocument $doc, string $slotContent): void
    {
        $slotElements = [];
        foreach ($doc->getElementsByTagName('slot') as $element) {
            $slotElements[] = $element;
        }

        foreach ($slotElements as $slotElement) {
            // Wrap slot content in a div to ensure all content (including comments) is preserved
            $wrappedContent = '<div>' . $slotContent . '</div>';
            $slotDoc = Dom\HTMLDocument::createFromString($wrappedContent, LIBXML_NOERROR);

            // Get the wrapper div's children (not the div itself)
            $wrapperDiv = $slotDoc->body->firstChild;
            if ($wrapperDiv && $wrapperDiv->nodeType === XML_ELEMENT_NODE) {
                // Import and insert nodes from inside the wrapper
                foreach ($wrapperDiv->childNodes as $child) {
                    $imported = $doc->importNode($child, true);
                    $slotElement->parentNode->insertBefore($imported, $slotElement);
                }
            }

            // Remove slot element
            $slotElement->parentNode->removeChild($slotElement);
        }
    }

    /**
     * Apply unmatched attributes to root element
     * For dynamic attributes, create placeholders that will be replaced with PHP
     */
    private function applyUnmatchedAttributes(Dom\Element $rootElement, array $attributes, array $usedAttributes, array $dynamicAttrNames = []): void
    {
        foreach ($attributes as $name => $value) {
            if (!in_array($name, $usedAttributes)) {
                if ($name === 'class') {
                    // Merge classes
                    $existingClass = $rootElement->getAttribute('class');
                    if (in_array($name, $dynamicAttrNames)) {
                        // Dynamic class - need to merge at runtime (not supported yet, treat as override)
                        $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributes) . '_ATTR';
                        $this->dynamicAttributes[$placeholder] = $value;
                        $rootElement->setAttribute('class', $placeholder);
                    } else {
                        $mergedClass = trim("$existingClass $value");
                        $rootElement->setAttribute('class', $mergedClass);
                    }
                } else {
                    if (in_array($name, $dynamicAttrNames)) {
                        // Dynamic attribute - create placeholder for PHP expression
                        $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributes) . '_ATTR';
                        $this->dynamicAttributes[$placeholder] = $value;
                        $rootElement->setAttribute($name, $placeholder);
                    } else {
                        // Static attribute - use value directly
                        $rootElement->setAttribute($name, $value);
                    }
                }
            }
        }
    }
}

// CLI entry point - only run when this file is executed directly
if (php_sapi_name() === 'cli' && isset($argv) && __FILE__ === realpath($argv[0])) {
    $projectRoot = dirname(__DIR__);
    $compiler = new BFGComponentCompiler($projectRoot);

    // Get command line arguments
    $args = array_slice($argv, 1);

    if (count($args) === 0) {
        // Compile all templates
        echo "Compiling all BFG templates...\n";

        // Recursively find all .bfg.php files and warn about plain .php files
        $sourceFiles = [];
        $plainPhpFiles = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($projectRoot . '/src/templates', RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                if (str_ends_with($filename, '.bfg.php')) {
                    $sourceFiles[] = $file->getPathname();
                } elseif (str_ends_with($filename, '.php')) {
                    $plainPhpFiles[] = $file->getPathname();
                }
            }
        }

        // Display warnings for plain .php files
        if (!empty($plainPhpFiles)) {
            echo "\n⚠️  Warning: Found plain .php files in src/templates/\n";
            echo "These files should use .bfg.php extension to be compiled:\n";
            foreach ($plainPhpFiles as $phpFile) {
                $relativePath = str_replace($projectRoot . '/src/templates/', '', $phpFile);
                echo "   • {$relativePath}\n";
            }
            echo "\n";
        }

        $compiled = 0;
        $failed = 0;

        foreach ($sourceFiles as $sourceFile) {
            try {
                $output = $compiler->compile($sourceFile);

                // Calculate output path: src/templates/... -> build/templates/...
                $relativePath = str_replace($projectRoot . '/src/templates/', '', $sourceFile);
                $outputPath = $projectRoot . '/build/templates/' . str_replace('.bfg.php', '.php', $relativePath);

                // Create output directory if needed
                $outputDir = dirname($outputPath);
                if (!is_dir($outputDir)) {
                    mkdir($outputDir, 0755, true);
                }

                // Write compiled output
                file_put_contents($outputPath, $output);

                echo "✓ " . basename($sourceFile) . " → " . str_replace($projectRoot . '/', '', $outputPath) . "\n";
                $compiled++;
            } catch (Exception $e) {
                echo "✗ " . basename($sourceFile) . " - Error: " . $e->getMessage() . "\n";
                $failed++;
            }
        }

        echo "\nCompiled: {$compiled} | Failed: {$failed}\n";
        exit($failed > 0 ? 1 : 0);
    } else {
        // Compile single file
        $sourceFile = $args[0];

        try {
            $output = $compiler->compile($sourceFile);

            // Calculate output path
            $relativePath = str_replace($projectRoot . '/src/templates/', '', $sourceFile);
            $outputPath = $projectRoot . '/build/templates/' . str_replace('.bfg.php', '.php', $relativePath);

            // Create output directory if needed
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Write compiled output
            file_put_contents($outputPath, $output);

            echo "✓ Compiled: {$outputPath}\n";
            exit(0);
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
