=== Bring Fraktguiden for WooCommerce ===
Author: Cove AS
Author URI: https://bringfraktguiden.no/
Contributors: forsvunnet, yratof
Donate link: https://bringfraktguiden.no/product/bring-fraktguiden-pro-for-woocommerce/
Tags: woocommerce, posten, frakt, sporing, bring
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 8.1
Requires Plugins: woocommerce
WC requires at least: 4.8.0
WC tested up to: 10.4.2
Stable tag: 1.12.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Bring Fraktguiden shows your customers the real price of shipping, from bring.no.

== Description ==

Bring Fraktguiden asks Bring for the price of the cart, so the shipping cost at checkout matches what you pay.

* All standard Bring services, from mailbox parcels to pallets.
* The price follows the cart. The plugin measures the products and packs them into parcels.
* Pickup points at checkout, with a map.
* Fallback prices for carts that fall through
* Works with the block checkout and the classic checkout.
* Easily extendible via hooks and actions

A setup page walks you through five steps, from the shipping zone to a test parcel. You need a free [Mybring](https://www.mybring.com) account for the prices.

We also sell a Pro license. It lets you book orders directly from WooCommerce, print labels, set a free shipping threshold per service and unlock pickup points. Read more at [bringfraktguiden.no](https://bringfraktguiden.no/).

Need help? See [support.bringfraktguiden.no](https://support.bringfraktguiden.no).

== Installation ==

1. Install the plugin from the WordPress plugin directory, or upload `bring-fraktguiden-for-woocommerce` to `/wp-content/plugins/`.
2. Activate the plugin.
3. Open the Bring Fraktguiden menu in the WordPress admin.

The setup page lists five steps. It adds Bring to a shipping zone, picks your services, sets a fallback price, connects your Mybring account and tests a parcel. Each step marks itself done when you finish it.

== Frequently Asked Questions ==
See faq.txt for frequently asked questions

== Changelog ==

= 1.12.0 =

* New Get Started page that sets up shipping zones, services and a fallback price in a few steps
* New Settings page, Booking page and Pro page
* New booking box on the order screen that books one shipment per order
* New bulk booking window on the orders list
* Added customs data for shipments that leave the country, with an HS code, a goods description and a net weight per product
* Added a search window that picks an HS code from the tariff
* Added a warning on the order screen when a shipment misses customs data
* Added NVIT transit data for shipments to a Norwegian transit postal code
* Added the customs consent to the booking screens
* Added a fallback shipping option for a checkout that Bring returns no price for
* Added a notice that names the setup steps left
* Added an offer to delete a free shipping method that hides the Bring options
* Added the Pakkeboks service, with its own pick-up points
* Added a return label to a booking, printed from the same button as the shipping label and from the bulk print action
* Added the pick-up point settings to the Settings page
* Added the distance to each pick-up point in the checkout picker
* Added a phone number check on a Pakkeboks and a Pakke hjem pluss order
* Added a setting that chooses the order statuses that allow a booking
* The Pro license now follows the shop domain, and the owner can move it from the Pro page
* A testing license now books in test mode only, and the booking page says why
* The buy link now carries the shop domain, so the license goes to the right shop
* The plugin now reads its settings from one store
* The booking box now sits at the top of the order screen, and groups its fields into cards
* The plugin now turns off a price customer number that Bring refuses, and names that number
* A booking from a copied database now stays a test booking
* The service wizard now asks about an RFID printer only for a parcel under 5 kg
* The zone form now suggests everywhere else only when the shop draws no zones of its own
* The checkout now keeps the shop theme out of the pick-up point picker
* WooCommerce is now a required plugin
* Four services now carry the names bring.no uses, such as Pakke hjem pluss and Pakke hjem i postkassen
* A cancelled order now shows no booking buttons, and the bulk booking skips it
* Removed the old Pro test mode, because a testing license now covers a test shop
* Completed the Norwegian translation
* Fixed a frozen checkout when the customer changes the pick-up point
* Fixed the customs and license scripts, which did not load on a live site
* Fixed a service that the service wizard cleared and then set again
* Fixed the test step, which needed a reload before it turned green
* Fixed a jump to the Pro page after the last setup step
* Fixed the pick-up points, which did not reload when the customer changed the street
* Fixed a pick-up point picker shown under a shipping option the customer did not choose
* Fixed an extra service sent with Pakke til hentested that Bring does not accept
* Fixed a warning that called a current customer number outdated
* Fixed the license refresh, which reported success when the server gave no answer
* Fixed the net weight placeholder of a variation, which now shows the parent weight
* Fixed the shipping time sent to Bring, which used a 12 hour clock and made 14:00 read as 02:00
* Fixed the lead time cutoff, which moved an order to the next day only when the lead time was more than 0

= 1.11.7 =

* Fixed bug with printing labels introduced by changing the loading order

= 1.11.6 =

* Fixed translations loaded too early

= 1.11.5 =

* Fixed permission issue for status page
* Fixed a bug where pickup points would not show in legacy mode

= 1.11.4 =

* Added a fix for a bug where the entire pick up point data was stored instead of just the id
* Added a fix for pickup points where localisation of the script sometimes happens after the script has loaded

= 1.11.2 =

* Added GPL license
* Fixed a bug with showing the pickup point in the block checkout

= 1.11.0 =

* Added support for WooCommerce Block Checkout
* Fixed a minor bug when creating an order in admin
* Added HPOS support for pick up point selection in the admin
* Improvments to the pick up point selector on checkout

= 1.10.12 =

* Added the support flag for modal settings to enable aditional settings for other plugins such as vipps and avarda checkout
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
