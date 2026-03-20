<?php
require_once __DIR__ . '/../src/Compiler/Parser/BFG_Document.php';

// Read booking template
$content = file_get_contents(__DIR__ . '/../src/templates/admin/pages/booking.bfg.php');

// Find the bfg-field.text with reference
preg_match('/<bfg-field\.text[^>]*id="booking_address_reference"[^>]*>/', $content, $matches);
$tagHtml = $matches[0] ?? '';

echo "Tag HTML:\n";
echo $tagHtml . "\n\n";

// Parse it
$doc = BFG_Document::createFromString($tagHtml, LIBXML_NOERROR);
$elements = $doc->getElementsByTagName('*');

foreach ($elements as $element) {
    if (strpos($element->tagName, 'bfg-field') !== false) {
        echo "Element: " . $element->tagName . "\n";
        foreach ($element->attributes as $attr) {
            echo "  Attribute '{$attr->name}' = '" . $attr->value . "'\n";
            if ($attr->name === 'description') {
                echo "  Hex: " . bin2hex($attr->value) . "\n";
                echo "  Bytes: ";
                for ($i = 0; $i < strlen($attr->value); $i++) {
                    printf("%02x ", ord($attr->value[$i]));
                }
                echo "\n";
            }
        }
    }
}
