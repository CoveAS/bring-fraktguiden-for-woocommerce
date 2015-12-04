=== Bring Fraktguiden for WooCommerce ===
Contributors: Driv Digital, Matt Gates
Donate link: http://drivdigital.no/
Tags: woocommerce, shipping, bring
Requires at least: 3.2.1
Tested up to: 4.0.1
Stable tag: ##VERSION##
License: The MIT License
License URI: http://opensource.org/licenses/MIT

== Description ==
Bring Fraktguiden provides shipping calculation based on rates from bring.no.
All standard shipping methods from Bring is bulit-in. Volume calculation based on product dimensions and weight.

Special thanks goes to Matt Gates for allowing us to use his code as a base for this plugin.

== Installation ==

Prerequisites

Make sure WooCommerce if configured with the following settings

* Dimension unit and weight unit (see WooCommerce > Settings > Products)
* Currency (see WooCommerce > Settings > General)

In order to calculate shipping, products in the stock must have dimensions and weight.

1. Upload `woocommerce-bring-fraktguiden` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to wp-admin > WooCommerce > Settings > Shipping
4. Enable and configure Bring Fraktguiden

== Frequently Asked Questions ==

Q: Rates does not show up on the cart page.

A: Reates are only shown when the customer has a valid zip code.

== Changelog ==

= 1.1.0 =

* Improved support for multi packaging.
* Fixed so fee is added to the rate.

= 1.0.0 =

* Initial release.

