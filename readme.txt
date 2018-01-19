=== Bring Fraktguiden for WooCommerce ===
Author: Driv Digital
Author URI: https://drivdigital.no/
Contributors: drivdigital, Matt Gates, oakidoaki
Donate link: http://drivdigital.no/
Tags: woocommerce, shipping, bring, fraktguiden
Requires at least: 4.5
Tested up to: 4.9.1
WC requires at least: 3.1
WC tested up to: 3.2.1
Stable tag: 1.4.0.2

Bring Fraktguiden provides shipping calculation based on rates from bring.no.

== Description ==
Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no

All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

Please consult the installation section and the changelog section before installing or upgrading.

> Special thanks goes to out to **Matt Gates** for starting this project and allowing us to build upon his original codebase.

If you'd like to contribute or report an issue, head over to: https://github.com/drivdigital/bring-fraktguiden-for-woocommerce

== Installation ==

When you install bring, you need to head to the settings page to start configuring Bring to your specifications.

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin > WooCommerce > Settings > Shipping
4. Select **Bring Fraktguiden**
5. Enable and configure Bring Fraktguiden

== Prerequisites ==

To ensure Bring will work correctly, you must set the following:

1. **Dimension and weight units**
    * WooCommerce > Settings > Products
2. **Currency**
    * WooCommerce > Settings > General
3. **Product dimentions**
    * Woocommerce > Products > Products

== Frequently Asked Questions ==

**Q: Why do rates not show up on the cart page?**
A: Rates are only shown when the customer has a valid postcode/zip code.

**Q: My products have dimensions, but they're being classified as heavy, why?**
A: Check your dimensions are set to the right unit values. Often, products are measured in MM but the default woocommerce unit is CM. This causes Bring to consider your products to be 10x their size.

== Changelog ==

= 1.4.0.2 =

* Fixed a bug affecting installations using php 5.6

= 1.4.0.1 =

* Fixed an issue where not all files were pushed to the SVN repo

= 1.4.0 =

* Added support to Klarna Checkout
* Improved the settings UI
* Updated Services based on the new 2018 Services from Bring
* Plugin has been made fully translatable finally
* Improved support for Woocommerce 3.2
* Additional options for end-user
* PRO version now contains Bring fallback methods for when the API can't be accessed
* Added more descriptions to services and features to help minimise the support you need to request.
* MyBring Booking has been added to the PRO version, which allows you to now book your orders with Bring directly on the order page.
* Added a stylesheet for styles. Those styles are used. Mostly for validation and error notifications when the postcode is wrong / invalid.
* Added the ability to disable the previously mentioned stylesheet, because you've already taken the styling issue into your own hands.
* Had a baby just before release of this version

= 1.3.2 =

* Removed GPL License
* Pro features are now bundled with the free version
* Support for WooCommerce 3.x
* Added license check as part of the PRO bundle
* Better support for Klarna Checkout

= 1.3.1 =

* Improved support for WooCommerce 2.6 Shipping Zones
* Uses the Wordpress' current language for Bring's reply messages, if available (thanks to oakidoaki)
* Misc. bug fixes

= 1.3.0 =

* Use GPL license
* Support WooCommerce 2.6
* WBF-30: Update the Fraktguiden product list.
* WBF-33: Improve support for sending to other countries - The support for sending from and to all Nordic countries has been improved.
* WBF-32: Fixed an issue where the checkout page would print a 'Invalid argument supplied for foreach.' message if no Fraktguiden services was provided by the shop (WP DEBUG MODE only)
* WBF-35: Option for using Product Name when displaying the services to customers
* WBF-37: Option for displaying the service description to customers

= 1.2.0 =

* WBF-16: Fixed packaging issue: Shipping options was not shown if one or more package surpassed 240 grams.
* WBF-17: Added support for recipient notification over SMS or E-Mail from Bring. Note that recipient notification needs to be activated after the upgrade (See: WooCommerce > settings > shipping > Bring Fraktguiden).
* WBF-18: Added WP filter for modifying the Fraktguiden request parameters.
* WBF-22: Added path to Woo Commerce logs in the plugin's options page.
* WBF-23: Fixed undefined variable bug when logging is enabled.

= 1.1.2 =

* Renamed plugin to bring-fraktguiden-for-woocommerce

= 1.1.1 =

* Requests to the bring api is now logged if debug is enabled (see settings screen)
* Misc. text changes

= 1.1.0 =

* Improved support for multi packaging.
* Fixed so fee is added to the rate.

= 1.0.0 =

* Initial release.
