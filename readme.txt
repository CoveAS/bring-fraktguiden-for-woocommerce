=== Bring Fraktguiden for WooCommerce ===
Author: Cove AS
Author URI: https://bringfraktguiden.no/
Contributors: forsvunnet, yratof
Donate link: https://bringfraktguiden.no/product/bring-fraktguiden-pro-for-woocommerce/
Tags: woocommerce, shipping, posten, frakt, sporing, sende, servicepakke, kolli, brev, forsendelse, postnord, nettbutikk
Requires at least: 5.6
Tested up to: 6.4.0
Requires PHP: 8.0
WC requires at least: 4.8.0
WC tested up to: 8.2.1
Stable tag: 1.10.3

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

= 1.10.3 =

* Fixed a bug that caused shipping rate prices to be 0
* Fixed a bug where in some themes the posten/bring logo would be huge

= 1.10.0 =

* Fixed bug with styles missing on checkout page
* Improved display of shipping options
* Made improved descriptions on by default
* Added setting to choose between net and list price
* Added setting to get prices using customer number or not
* Added setting for language
* If price has been set on all services the plugin will no longer request prices from the api
* New design for pick up points
* Added logo and environmental tag for shipping rates
* Added option to make shipping options full width on the checkout page
* Added option to select which map provider to use for pick up points
* Added option to chose the new or legacy design for pick up points
* Changed usage of shipping guide rest api from using GET to POST method
* Removed bring_fraktguiden_standard_url_params filter
* Added bring_fraktguiden_shipping_guide_request_body filter
* Removed WooCommerce cart-shipping template

= 1.9.5 =

* Fixed bug with downloading labels

= 1.9.4 =

* Improved error handling for customer number api integration
* Fixed issue with settings not detecting pro activated correctly
* Added method for license server to trigger license check on purchase of new license
* Added namespacing for most of the classes and improved class autoloader

= 1.9.3 =

* Fixed a bug where all value added services would always be used with bulk booking
* Added Home delivery parcel to available services

= 1.9.2 =

* Fixed a bug where the customer number would not be included in API requests

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

See changelog.txt for older entries
