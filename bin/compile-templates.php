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

// Load dependencies
require_once __DIR__ . '/../src/Compiler/Parser/BFG_Document.php';
require_once __DIR__ . '/../src/Compiler/AttributeManager.php';
require_once __DIR__ . '/../src/Compiler/TemplateLoader.php';
require_once __DIR__ . '/../src/Compiler/PhpCodeGenerator.php';
require_once __DIR__ . '/../src/Compiler/Processors/ProcessorInterface.php';
require_once __DIR__ . '/../src/Compiler/Processors/TranslationProcessor.php';
require_once __DIR__ . '/../src/Compiler/Processors/TextElementProcessor.php';
require_once __DIR__ . '/../src/Compiler/Processors/AttributeProcessor.php';
require_once __DIR__ . '/../src/Compiler/Processors/ConditionalProcessor.php';
require_once __DIR__ . '/../src/Compiler/Processors/SlotProcessor.php';

class BFGComponentCompiler
{
    private string $projectRoot;
    private string $componentsDir;
    private string $textDomain = 'bring-fraktguiden-for-woocommerce';
    private ?BFG_AttributeManager $currentAttributeManager = null;
    private array $dynamicAttributePlaceholders = [];
    private BFG_TemplateLoader $templateLoader;
    private BFG_PhpCodeGenerator $phpGenerator;
    private BFG_TranslationProcessor $translationProcessor;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->componentsDir = $projectRoot . '/src/components';
        $this->templateLoader = new BFG_TemplateLoader($this->componentsDir);
        $this->phpGenerator = new BFG_PhpCodeGenerator($this->textDomain);
        $this->translationProcessor = new BFG_TranslationProcessor($this->phpGenerator);
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
        // Use escape-proof placeholders that won't be HTML-entity-encoded
        $phpTagMap = [];
        $sourceContent = preg_replace_callback(
            '/<\?php(.*?)\?>/s',
            function($matches) use (&$phpTagMap) {
                $placeholder = '__BFG_PHP_PRESERVE_' . count($phpTagMap) . '__';
                $phpTagMap[$placeholder] = '<?php' . $matches[1] . '?>';
                return $placeholder;
            },
            $sourceContent
        );

        // Parse source file as HTML
        $sourceDoc = BFG_Document::createFromString($sourceContent, LIBXML_NOERROR);

        // Process standalone <t> tags for translation (before component processing)
        $this->translationProcessor->process($sourceDoc);

        // Find all <bfg-*> component tags and process them
        $this->processComponentTags($sourceDoc);

        // Extract only the body content (avoid <!DOCTYPE>, <html>, etc.)
        $output = '';
        foreach ($sourceDoc->body->childNodes as $node) {
            $output .= $sourceDoc->saveHTML($node);
        }

        // Replace dynamic attribute placeholders with PHP code
        foreach ($this->dynamicAttributePlaceholders as $placeholder => $expression) {
            $output = str_replace($placeholder, '<?php echo ' . $expression . '; ?>', $output);
        }

        // Restore original PHP tags from escape-proof placeholders
        foreach ($phpTagMap as $placeholder => $phpTag) {
            $output = str_replace($placeholder, $phpTag, $output);
        }

        return $output;
    }

    /**
     * Process all <bfg-*> component tags in the document
     */
    private function processComponentTags(BFG_Document $doc): void
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
    private function processComponentTag(BFG_ElementNode $tag, BFG_Document $sourceDoc): void
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
        $templateContent = $this->templateLoader->load($componentName);
        $templateDoc = BFG_Document::createFromString($templateContent, LIBXML_NOERROR);

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

        // Create AttributeManager for this component
        $attrManager = new BFG_AttributeManager($attributes, $dynamicAttrNames);

        // Mark 'slot' as used so it doesn't get passed through as an attribute
        $attrManager->markAsUsed('slot');

        // Set as current manager
        $this->currentAttributeManager = $attrManager;

        // Apply replacements in order using processors
        $textProcessor = new BFG_TextElementProcessor($this->phpGenerator, $attrManager);
        $textProcessor->process($templateDoc);

        $attrProcessor = new BFG_AttributeProcessor($attrManager, $this->dynamicAttributePlaceholders);
        $attrProcessor->process($templateDoc);

        $conditionalProcessor = new BFG_ConditionalProcessor($this->phpGenerator, $attrManager);
        $conditionalProcessor->process($templateDoc);

        $slotProcessor = new BFG_SlotProcessor($slotContent);
        $slotProcessor->process($templateDoc);

        // Save used attributes before processing nested components
        $savedUsedAttributes = $attrManager->saveUsedState();

        // Process any nested component tags that were introduced by slot content
        $this->processComponentTags($templateDoc);

        // Restore used attributes after nested component processing
        $attrManager->restoreUsedState($savedUsedAttributes);

        // Clean up any remaining <else> tags (they should have been removed by conditional processing)
        $this->cleanupElseTags($templateDoc);

        // Get root element and apply unmatched attributes
        $rootElement = $templateDoc->body->firstChild;
        while ($rootElement && $rootElement->nodeType !== XML_ELEMENT_NODE) {
            $rootElement = $rootElement->nextSibling;
        }

        if ($rootElement instanceof BFG_ElementNode) {
            $this->applyUnmatchedAttributes($rootElement, $attrManager);
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
     * Clean up any remaining <else> tags (safety cleanup)
     */
    private function cleanupElseTags(BFG_Document $doc): void
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
     * Apply unmatched attributes to root element
     * For dynamic attributes, create placeholders that will be replaced with PHP
     */
    private function applyUnmatchedAttributes(BFG_ElementNode $rootElement, BFG_AttributeManager $attrManager): void
    {
        $unused = $attrManager->getUnused();
        foreach ($unused as $name => $value) {
            if ($name === 'class') {
                // Merge classes
                $existingClass = $rootElement->getAttribute('class');
                if ($attrManager->isDynamic($name)) {
                    // Dynamic class - need to merge at runtime (not supported yet, treat as override)
                    $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributePlaceholders) . '_ATTR';
                    $this->dynamicAttributePlaceholders[$placeholder] = $value;
                    $rootElement->setAttribute('class', $placeholder);
                } else {
                    $mergedClass = trim("$existingClass $value");
                    $rootElement->setAttribute('class', $mergedClass);
                }
            } else {
                if ($attrManager->isDynamic($name)) {
                    // Dynamic attribute - create placeholder for PHP expression
                    $placeholder = 'BFG_DYNAMIC_' . count($this->dynamicAttributePlaceholders) . '_ATTR';
                    $this->dynamicAttributePlaceholders[$placeholder] = $value;
                    $rootElement->setAttribute($name, $placeholder);
                } else {
                    // Static attribute - use value directly
                    $rootElement->setAttribute($name, $value);
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
