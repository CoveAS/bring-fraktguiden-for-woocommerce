=== Bring Fraktguiden for WooCommerce ===
Author: Driv Digital
Author URI: https://drivdigital.no
Contributors: drivdigital, Matt Gates, oakidoaki
Donate link: http://drivdigital.no/
Tags: woocommerce, shipping, bring, fraktguiden
Requires at least: 3.2
Tested up to: 4.5
WC requires at least: 2.4
WC tested up to: 2.6
Stable tag: 1.3.2

Bring Fraktguiden provides shipping calculation based on rates from bring.no.

== Description ==
Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no.
All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

Please consult the installation section and the changelog section before installing or upgrading.

Special thanks goes to Matt Gates for allowing us to use his code as a base for this plugin.

If you want to contribute or file an issue, please go to: https://github.com/drivdigital/bring-fraktguiden-for-woocommerce

== Installation ==

= Prerequisites =

Make sure WooCommerce is configured with:

* Dimension unit and weight unit (see WooCommerce > Settings > Products)
* Currency (see WooCommerce > Settings > General)
* Products must have sizes and weights set – without this the calculation cannot be performed

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin > WooCommerce > Settings > Shipping
4. Select Bring Fraktguiden
5. Enable and configure Bring Fraktguiden

== Frequently Asked Questions ==

Q: Why do rates not show up on the cart page?

A: Rates are only shown when the customer has a valid zip code.

== Changelog ==

= 1.0.0 =

* Initial release
