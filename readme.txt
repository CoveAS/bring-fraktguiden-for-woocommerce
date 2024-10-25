=== Bring Fraktguiden for WooCommerce ===
Author: Cove AS
Author URI: https://bringfraktguiden.no/
Contributors: forsvunnet, yratof
Donate link: https://bringfraktguiden.no/product/bring-fraktguiden-pro-for-woocommerce/
Tags: woocommerce, shipping, posten, frakt, sporing, sende, servicepakke, kolli, brev, forsendelse, postnord, nettbutikk
Requires at least: 5.6
Tested up to: 6.6.1
Requires PHP: 8.1
WC requires at least: 4.8.0
WC tested up to: 9.2.2
Stable tag: 1.10.11

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

= 1.10.11 =

* Updated PDF merger package

= 1.10.10 =

* Better error handling for bulk booking
* Added VAS 1280 Signature required for Mailbox services
* Adjusted texts for mailbox services to say 5 kg instead of 2 kg

= 1.10.9 =

* Changed booking column on the orders page to buttons that can be used to book or print labels.

= 1.10.8 =

* Fixed missing bulk action  and booking column on orders when using HPOS
* Added fallback to legacy view for pickup points when klarna checkout is chosen
* Added keyboard support for the new pick up point modal

= 1.10.7 =

* Fixed bugs related to HPOS
* Fixed a bug when updating package dimensions in the booking window

= 1.10.5 =

* Fixed some warning messages and notices in php 8.2 and above

= 1.10.4 =

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

See changelog.txt for older entries
