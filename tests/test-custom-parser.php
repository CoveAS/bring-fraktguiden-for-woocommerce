#!/usr/bin/env php
<?php
/**
 * Integration test for BFG_Document custom parser
 *
 * Tests that custom tags are preserved inside <select> elements.
 */

require_once __DIR__ . '/../src/Compiler/Parser/BFG_Document.php';

echo "Testing BFG_Document custom parser...\n\n";

// Test 1: Parse field.select.bfgc.php and verify custom tags are preserved
echo "Test 1: Parse field.select.bfgc.php\n";
echo "=====================================\n";

$templatePath = __DIR__ . '/../src/components/field.select.bfgc.php';
if (!file_exists($templatePath)) {
    echo "ERROR: Template file not found: $templatePath\n";
    exit(1);
}

$html = file_get_contents($templatePath);
echo "Input HTML length: " . strlen($html) . " bytes\n\n";

// Parse with custom parser
$doc = BFG_Document::createFromString($html);

// Test: Find <if> tags
$ifElements = $doc->getElementsByTagName('if');
echo "Found " . count($ifElements) . " <if> elements\n";
if (count($ifElements) === 0) {
    echo "ERROR: No <if> elements found! Parser failed to preserve custom tags.\n";
    exit(1);
}

foreach ($ifElements as $i => $element) {
    echo "  - <if> #" . ($i + 1) . ": ";
    $attrs = $element->getAttributes();
    foreach ($attrs as $attr) {
        echo $attr->name . '="' . $attr->value . '" ';
    }
    echo "\n";
}
echo "\n";

// Test: Find <slot> tags
$slotElements = $doc->getElementsByTagName('slot');
echo "Found " . count($slotElements) . " <slot> elements\n";
if (count($slotElements) === 0) {
    echo "ERROR: No <slot> elements found! Parser failed to preserve custom tags.\n";
    exit(1);
}
echo "\n";

// Test: Find <t> tags
$tElements = $doc->getElementsByTagName('t');
echo "Found " . count($tElements) . " <t> elements\n";
echo "\n";

// Test: Verify <select> element structure
$selectElements = $doc->getElementsByTagName('select');
echo "Found " . count($selectElements) . " <select> elements\n";
if (count($selectElements) > 0) {
    $select = $selectElements[0];
    echo "  <select> has " . count($select->childNodes) . " child nodes:\n";

    foreach ($select->childNodes as $i => $child) {
        if ($child instanceof BFG_ElementNode) {
            echo "    " . ($i + 1) . ". <" . $child->tagName . ">\n";
        } elseif ($child instanceof BFG_TextNode) {
            $preview = trim($child->content);
            if ($preview !== '') {
                $preview = substr($preview, 0, 20);
                echo "    " . ($i + 1) . ". Text: \"$preview...\"\n";
            }
        }
    }

    // Check if <if> is a child of <select>
    $hasIfChild = false;
    $hasSlotChild = false;
    foreach ($select->childNodes as $child) {
        if ($child instanceof BFG_ElementNode) {
            if ($child->tagName === 'if') {
                $hasIfChild = true;
            }
            if ($child->tagName === 'slot') {
                $hasSlotChild = true;
            }
        }
    }

    if ($hasIfChild) {
        echo "\n  ✓ SUCCESS: <if> tag preserved inside <select>!\n";
    } else {
        echo "\n  ERROR: <if> tag NOT found inside <select>!\n";
        exit(1);
    }

    if ($hasSlotChild) {
        echo "  ✓ SUCCESS: <slot> tag preserved inside <select>!\n";
    } else {
        echo "\n  ERROR: <slot> tag NOT found inside <select>!\n";
        exit(1);
    }
}
echo "\n";

// Test 2: Round-trip serialization
echo "Test 2: Round-trip serialization\n";
echo "=================================\n";

$output = $doc->saveHTML();
echo "Output HTML length: " . strlen($output) . " bytes\n";

// Verify output contains custom tags
if (strpos($output, '<if') === false) {
    echo "ERROR: Output missing <if> tags!\n";
    exit(1);
}
if (strpos($output, '<slot') === false && strpos($output, '<slot>') === false) {
    echo "ERROR: Output missing <slot> tags!\n";
    exit(1);
}
if (strpos($output, '<t>') === false) {
    echo "ERROR: Output missing <t> tags!\n";
    exit(1);
}

echo "✓ All custom tags present in output\n\n";

// Test 3: Simple custom tag preservation test
echo "Test 3: Simple preservation test\n";
echo "=================================\n";

$simpleHtml = '<select><if :placeholder><option><t>placeholder</t></option></if><slot></slot></select>';
$simpleDoc = BFG_Document::createFromString($simpleHtml);
$simpleOutput = $simpleDoc->saveHTML();

echo "Input:  $simpleHtml\n";
echo "Output: $simpleOutput\n";

if (strpos($simpleOutput, '<if') !== false && strpos($simpleOutput, '<slot') !== false) {
    echo "✓ Custom tags preserved in simple test\n";
} else {
    echo "ERROR: Custom tags not preserved in simple test!\n";
    exit(1);
}
echo "\n";

// Test 4: Test DOM manipulation API
echo "Test 4: DOM manipulation API\n";
echo "=============================\n";

$testHtml = '<div><slot/></div>';
$testDoc = BFG_Document::createFromString($testHtml);

// Test getElementsByTagName
$divs = $testDoc->getElementsByTagName('div');
echo "Found " . count($divs) . " <div> elements\n";

$slots = $testDoc->getElementsByTagName('slot');
echo "Found " . count($slots) . " <slot> elements\n";

if (count($slots) > 0) {
    $slot = $slots[0];

    // Test setAttribute/getAttribute
    $slot->setAttribute('name', 'content');
    $value = $slot->getAttribute('name');
    echo "setAttribute/getAttribute: $value\n";

    if ($value !== 'content') {
        echo "ERROR: setAttribute/getAttribute failed!\n";
        exit(1);
    }

    echo "✓ DOM manipulation API works\n";
}
echo "\n";

// Test 5: Test PHP block preservation
echo "Test 5: PHP block preservation\n";
echo "===============================\n";

$phpHtml = '<div><?php echo $var; ?></div>';
$phpDoc = BFG_Document::createFromString($phpHtml);
$phpOutput = $phpDoc->saveHTML();

echo "Input:  $phpHtml\n";
echo "Output: $phpOutput\n";

if (strpos($phpOutput, '<?php') !== false && strpos($phpOutput, '?>') !== false) {
    echo "✓ PHP blocks preserved\n";
} else {
    echo "ERROR: PHP blocks not preserved!\n";
    exit(1);
}
echo "\n";

echo "==========================================\n";
echo "All tests passed! ✓\n";
echo "==========================================\n";
