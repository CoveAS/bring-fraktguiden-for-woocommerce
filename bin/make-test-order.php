<?php
/**
 * Make an order that the Bring booking box accepts, for manual testing.
 *
 * Run it with WP-CLI, from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/bring-fraktguiden-for-woocommerce/bin/make-test-order.php
 *
 * Five optional arguments set the case to test:
 *
 *   1 postal code   The shipping postal code. 9008 is Tromso, an NVIT route.
 *   2 hs code       The HS code of the product. Pass "none" for a product
 *                   without one.
 *   3 weight        The weight in kilograms. Pass 0 for a product without one.
 *   4 price         The price of one unit.
 *   5 quantity      The number of units on the line.
 *
 * The script reuses a product per case, so it makes one product for each set of
 * values and a new order every run.
 */

use BringFraktguiden\Customs\CustomsCheck;
use BringFraktguiden\Customs\HsCode;
use BringFraktguiden\Customs\NetWeight;

$postcode = $args[0] ?? '9008';
$hs_code  = 'none' === ($args[1] ?? '') ? '' : ($args[1] ?? '62034000');
$weight   = (float) ($args[2] ?? 0.4);
$price    = (float) ($args[3] ?? 299);
$quantity = (int) ($args[4] ?? 2);

$sku = sprintf('bring-test-%s-%s', $hs_code ?: 'nohs', $weight ?: 'noweight');

$product_id = wc_get_product_id_by_sku($sku);
$product    = $product_id ? wc_get_product($product_id) : new WC_Product_Simple();

$product->set_name(sprintf('Bring test product (%s)', $sku));
$product->set_sku($sku);
$product->set_regular_price($price);
$product->set_weight($weight ?: '');
$product->set_manage_stock(false);
$product->update_meta_data(HsCode::META, $hs_code);
$product->update_meta_data(NetWeight::META, '');
$product->save();

$order = wc_create_order();

$order->add_product($product, $quantity);

$address = [
	'first_name' => 'Test',
	'last_name'  => 'Kunde',
	'address_1'  => 'Storgata 1',
	'city'       => 'Test',
	'postcode'   => $postcode,
	'country'    => 'NO',
	'email'      => 'test@example.com',
	'phone'      => '99999999',
];

$order->set_address($address, 'billing');
$order->set_address($address, 'shipping');

// The booking box appears only when a shipping line names a Bring method. The
// bring_product meta names the service the booking asks Bring for.
$shipping = new WC_Order_Item_Shipping();
$shipping->set_method_title('Bring test service');
$shipping->set_method_id('bring_fraktguiden');
$shipping->set_total(0);
$shipping->add_meta_data('bring_product', '5800', true);
$order->add_item($shipping);

$order->calculate_totals();
$order->set_status('processing');
$order->save();

printf("order %d, %s\n", $order->get_id(), $order->get_edit_order_url());
printf("ship to %s, %d x %s at %s\n", $postcode, $quantity, $sku, $price);

$problems = CustomsCheck::problems($order);

if (!$problems) {
	echo "customs check: ok\n";

	return;
}

echo "customs check:\n";

foreach ($problems as $item_id => $list) {
	foreach ($list as $problem) {
		printf("  %s: %s\n", $order->get_item($item_id)->get_name(), $problem->message());
	}
}
