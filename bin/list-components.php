#!/usr/bin/env php
<?php
/**
 * BFG Component Lister
 *
 * Lists all available BFG components (.bfgc.php files)
 *
 * Usage:
 *   php bin/list-components.php
 *   php bin/list-components.php --detailed
 */

$projectRoot = dirname(__DIR__);
$componentsDir = $projectRoot . '/src/components';

// Parse command line arguments
$detailed = in_array('--detailed', $argv);

if (!is_dir($componentsDir)) {
    echo "Error: Components directory not found: {$componentsDir}\n";
    exit(1);
}

// Find all .bfgc.php files
$components = glob($componentsDir . '/*.bfgc.php');

if (empty($components)) {
    echo "No components found in {$componentsDir}\n";
    exit(0);
}

// Sort alphabetically
sort($components);

echo "BFG Components (" . count($components) . " found)\n";
echo str_repeat('=', 50) . "\n\n";

foreach ($components as $componentPath) {
    $basename = basename($componentPath, '.bfgc.php');
    $tagName = 'bfg-' . str_replace('.', '.', $basename);

    if ($detailed) {
        echo "Component: {$basename}\n";
        echo "  Tag name: <{$tagName}>\n";
        echo "  File: " . str_replace($projectRoot . '/', '', $componentPath) . "\n";

        // Extract docblock if available
        $content = file_get_contents($componentPath);
        if (preg_match('/\/\*\*(.*?)\*\//s', $content, $matches)) {
            $docblock = $matches[1];
            // Get first line of description (after * )
            if (preg_match('/^\s*\*\s*(.+?)$/m', $docblock, $descMatch)) {
                $description = trim($descMatch[1]);
                if ($description && !str_starts_with($description, '@')) {
                    echo "  Description: {$description}\n";
                }
            }
        }
        echo "\n";
    } else {
        echo "  • {$basename}\n";
        echo "    <{$tagName}>\n";
    }
}

if (!$detailed) {
    echo "\nUse --detailed flag for more information\n";
}
