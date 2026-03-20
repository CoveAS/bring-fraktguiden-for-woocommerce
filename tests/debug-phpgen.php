<?php
require_once __DIR__ . '/../src/Compiler/PhpCodeGenerator.php';

$text = "The store's reference printed on the shipping label. Usually {order_id}, but can also be {products}.";

echo "Input text:\n";
echo $text . "\n\n";
echo "Input hex: " . bin2hex($text) . "\n\n";

$generator = new BFG_PhpCodeGenerator('bring-fraktguiden-for-woocommerce');
$phpCode = $generator->translatableText($text);

echo "Generated PHP code:\n";
echo $phpCode . "\n\n";
echo "PHP code hex: " . bin2hex($phpCode) . "\n";
