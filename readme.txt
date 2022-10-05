=== Bring Fraktguiden for WooCommerce ===
Author: Cove AS
Author URI: https://bringfraktguiden.no/
Contributors: forsvunnet, yratof
Donate link: https://bringfraktguiden.no/product/bring-fraktguiden-pro-for-woocommerce/
Tags: woocommerce, shipping, posten, frakt, sporing, sende, servicepakke, kolli, brev, forsendelse, postnord, nettbutikk
Requires at least: 5.6
Tested up to: 6.0.1
Requires PHP: 8.0
WC requires at least: 4.8.0
WC tested up to: 6.7.0
Stable tag: 1.9.2

Bring Fraktguiden provides shipping calculation based on rates from bring.no.

== Description ==

Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.
**NB! You must have a [Mybring.com](https://www.mybring.com) account and API credentials to use this plugin.**

== Installation ==

When you install Bring, you need to head to the settings page to start configuring Bring to your specifications.

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory or install through Wordpress
2. Activate the plugin
3. WooCommerce → Settings → Shipping
4. Add Bring Fraktguiden to a shipping zone
5. Go to WooCommerce → Bring settings
6. Enter your Mybring API credentials
7. Go to WooCommerce → Bring settings → Shipping options
5. Select the bring products`*` you want to enable
6. Start shipping!

`*` The most commonly used bring products are "Pakke til hentested" and/or "Pakke i postkassen"

== Pro version ==

We offer a [PRO license](https://bringfraktguiden.no/) adds more features to the plugin.

* Book orders with mybring directly from WooCommerce
* Configure free shipping threshold for bring services
* Enable pickup points for supported services

Read more about the pro features on [https://bringfraktguiden.no/](https://bringfraktguiden.no/).

== Prerequisites ==

To ensure Bring will work correctly, you must set the following:

1. **Dimension and weight units**
    * WooCommerce > Settings > Products > Set your default dimensions

2. **Currency**
    * WooCommerce > Settings > General > Set store currency

3. **Product dimensions**
    * Woocommerce > Products > Select a products > Set the dimensions for the item
        - This will fallback to the options in the Bring Settings if you don't set them here.


== Frequently Asked Questions ==
See faq.txt for frequently asked questions

== Changelog ==

= 1.9.1 =

* Fixed a bug where the customer number would not be included in API requests

= 1.9.1 =

* Improved support for WooCommerce subscriptions
* Fixed a bug where bag on door checkbox on checkout would show even if the option had not been enabled

= 1.9.0 =

* Added bag on door support for mailbox method
* Added individual verification and id verification value added service
* Added setting to enable booking for orders that does not use a bring shipping method
* Added bring product select box to booking items
* Added validation for booking settings
* Upgraded vuejs from version 2 to 3

= 1.8.8 =

* Fixed some unsupported operand type errors.
* Fixed a bug with decimals for shipping rates that caused incorrect tax calculation for shops that use 0 decimal places.

= 1.8.6 =

* Added PHP 8 support
* Fixed a bug with the setting to change order status after printing labels where it would update old orders
* Added support for booking of HD services

= 1.8.5 =

* Added filter bring_fraktguiden_get_consignment_recipient_address
* Fixed error when mass booking Mailbox with tracking
* Fixed error when no date was set for home delivery

= 1.8.2 =

* Added new feature for alternative delivery dates for home delivery
* Added ETA support for shipping rates
* Added support for services that does not return shipping prices
* Made the product tester always available
* Added lead time settings
* Booking - Added {products} parameter for order reference
* Booking - Changed action for rendering PDF's
* Fixed syntax error

See changelog.txt for more
