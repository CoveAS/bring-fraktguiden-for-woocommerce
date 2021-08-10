=== Bring Fraktguiden for WooCommerce ===
Author: Cove AS
Author URI: https://cove.no/
Contributors: forsvunnet, yratof
Donate link: https://bringfraktguiden.no/product/bring-fraktguiden-pro-for-woocommerce/
Tags: woocommerce, shipping, posten, frakt, sporing, sende, servicepakke, kolli, brev, forsendelse, postnord, nettbutikk
Requires at least: 4.5
Tested up to: 5.8
Requires PHP: 8.0
WC requires at least: 3.4.0
WC tested up to: 5.5.2
Stable tag: 1.8.6

Bring Fraktguiden provides shipping calculation based on rates from bring.no.

== Description ==

Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.
**NB! You must have a [Mybring.com](https://www.mybring.com) account and API credentials to use this plugin.**

== Installation ==

When you install Bring, you need to head to the settings page to start configuring Bring to your specifications.

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory or install through Wordpress
2. Activate the plugin
3. Go to Dashboard >WooCommerce > Bring settings
4. Enter your Mybring API credentials
5. Configure Bring Fraktguiden with your address and select the services you would like to use.
6. Start shipping!

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

= 1.7.12 =

* Added pickup selection for pakke til hentested in admin
* Fixed issue with fallback solution for heavy consignments

= 1.7.9 =

* Fixed a bug with the implementation of the service table for WC 4.0.0 which caused service configuration not to save.
* Made the plugin compatible with WooCommerce 4.0.0
* Fixed default selection of customer number for booking
* Fixed fatal bug during booking

= 1.7.6 =

* Removed norgespakke because it is no longer supported
* Fixed an issue with pickup points for klimanøytral servicepakke
* Fixed bug with post code validation that caused validation intended for Norway to be applied on other some other countries
* Added missing implementation of E-varsling and 2084 value added services

= 1.7.5 =

* Fixed merging of ZPL files
* Fixed tocountry bug with the product tester

= 1.7.4 =

* Fixed download of ZPL files from orders using mailbox with tracking
* Added special handling for mailbox services which fixes booking issues
* Various interface improvements

= 1.7.2 =

* Improved bring booking logging
* Fixed the location of the shipping options template
* Reformed readme to meet standards
* Added missing translations

See changelog.txt for more
