<?php
/**
 * Make an order that the Bring booking box accepts, for manual testing.
 *
 * Run it with WP-CLI, from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/bring-fraktguiden-for-woocommerce/bin/make-test-order.php
 *
 * The first argument is the shipping postal code. 9008 is Tromso, an NVIT route.
 *
 * Every argument after it describes one order line, as four values separated by
 * commas:
 *
 *   hs code    The HS code of the product. Pass "none" for a product without one.
 *   weight     The weight in kilograms. Pass 0 for a product without one.
 *   price      The price of one unit.
 *   quantity   The number of units on the line.
 *
 * An order with three faulty lines:
 *
 *   wp eval-file .../make-test-order.php 9008 none,0.4,299,1 62034000,0,299,1 62034000,0.4,0,1
 *
 * The script reuses a product per case, so it makes one product for each set of
 * values and a new order every run.
 */

use BringFraktguiden\Customs\CustomsCheck;
use BringFraktguiden\Customs\CustomsRoute;
use BringFraktguiden\Customs\HsCode;
use BringFraktguiden\Customs\NetWeight;

$postcode = $args[0] ?? '9008';
$specs    = array_slice($args, 1) ?: ['62034000,0.4,299,2'];

$order = wc_create_order();

foreach ($specs as $spec) {
	[$hs_code, $weight, $price, $quantity] = array_pad(explode(',', $spec), 4, '');

	$hs_code  = 'none' === $hs_code ? '' : $hs_code;
	$weight   = (float) $weight;
	$price    = (float) $price;
	$quantity = max(1, (int) $quantity);

	$sku = sprintf('bring-test-%s-%s-%s', $hs_code ?: 'nohs', $weight ?: 'noweight', $price ?: 'noprice');

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

	$order->add_product($product, $quantity);

	printf("line %d x %s at %s\n", $quantity, $sku, $price);
}

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

printf("order %d, ship to %s\n%s\n", $order->get_id(), $postcode, $order->get_edit_order_url());

$problems = CustomsCheck::problems($order, CustomsRoute::for_order($order, '5800'));

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
