=== Bring Fraktguiden for WooCommerce ===
Author: Driv Digital
Author URI: https://drivdigital.no/
Contributors: drivdigital, Matt Gates, oakidoaki
Donate link: http://drivdigital.no/
Tags: woocommerce, shipping, bring, fraktguiden
Requires at least: 4.5
Tested up to: 4.9.1
Requires PHP: 5.6
WC requires at least: 3.4.0
WC tested up to: 3.4.2
Stable tag: 1.5.2

Bring Fraktguiden provides shipping calculation based on rates from bring.no.

== Description ==
Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no

All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

Please consult the installation section and the changelog section before installing or upgrading.

> Special thanks goes to out to **Matt Gates** for starting this project and allowing us to build upon his original codebase.

== Shipping Zones ==

Please note that it is **now required** to set up [Shipping Zones](https://docs.woocommerce.com/document/setting-up-shipping-zones/) for the shipping
calculation to work.

When creating a shipping zone, select **Bring Fraktguiden** as the method available.

== Installation ==

When you install Bring, you need to head to the settings page to start configuring Bring to your specifications.

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory or install through Wordpress
2. Activate the plugin
3. Go to Dashboard >WooCommerce > Settings > Shipping
4. Select **Bring Fraktguiden** on the sub menu
5. Configure Bring Fraktguiden with your address and pricing
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

**Why do rates not show up on the cart page?**
Bring rates are only shown when the customer has entered a valid postcode. Commonly customers have either entered the wrong postcode or live outside of your Bring postcode settings.

**But the postcode is valid and the rate still isn't showing?**
If you've entered any MyBring details, try removing them. If Bring shows up on the cart page after they're removed, it suggests that your details may be incorrect. If it's still not showing after that, you should check that all the settings are correctly filled out.

**What do I get from Bring Pro that I can't from the Free version?**
Good question! Bring Pro enables you to customise your customers Bring experience:
    - You can customise the price of each shipping method
    - You can set targets that enable free shipping if your customers spend a certain amount ( For example, Free shipping when you spend 1000 ).
    - You can book your shippment through MyBring directly on your orders page.
    - You can change the services offered to customers to their nearest collection point

**'Minipakke' and 'Småpakke' are no longer available? What happened?**
These have been replaced with new and exciting shipping option called 'Pakke i postkassen'! You can read more about it here: https://www.bring.no/radgivning/netthandel/pakkeipostkassen. In essence, it's  a streamlined version of the previous options available.

**Where are 'A-Mail' and 'B-Mail' options?**
Bring revealed that in 2018, A-mail and B-mail have been merged into a new service called "Brev". The idea is that we just don't post as much as we used to due to the digitalisation of mail. Combining A & B Mail means that daily flights to deliver post goes from 10 flights per day to 2 flights. Pretty neat right?

**My products have dimensions, but they're being classified as heavy, why?**
Check your dimensions are set to the right unit values. Often, products are measured in MM but the default woocommerce unit is CM. This causes Bring to consider your products to be 10x their size.

**I created a custom user role to manage Bring shippments, but they can't access the PDFs?**
Shipping information such as PDFs are private, so we limit those to only certain user roles. Those roles/capabilities are: `administrator`, `manage_woocommerce`, `warehouse_team` and a custom capability called `bring_labels`. If you create a new role and only want them to access your orders, create a roll called `warehouse_team`. If you already have a role created, give them the `bring_labels` capability.

**I activate the plugin and everything goes white!**
A rare occasion, but when this happens, it means that your server is running an old version of PHP – You can find out what version of PHP you're using by heading to **Woocommerce -> Status**, this will let you know if you're running out-of-date software. The best way to recover from this [WSOD](https://codex.wordpress.org/Common_WordPress_Errors#The_White_Screen_of_Death) is to use the FileEditor, PHPMyadmin, sFTP or SSH to rename the plugin, thus disabling it in wordpress. You should then consider upgrading your PHP version by contacting your Host provider.

**What kind of support do you provide?**
We monitor the Support forum of this plugin frequently to assist you in running your shop smoothly. You can visit the [Support](https://wordpress.org/support/plugin/bring-fraktguiden-for-woocommerce) section to read about any ongoing issues, or you can [Submit a new issue](https://wordpress.org/support/plugin/bring-fraktguiden-for-woocommerce#new-post) if you've discovered a problem.

**Do you have a github where I can contribute?**
Yes, yes we do. [Bring Github](https://github.com/drivdigital/bring-fraktguiden-for-woocommerce). Please make pull requests to the Develop branch. Pull the develop branch, make any changes you see fit & create a PR.



== Changelog ==

= 1.5.2
* Added compability with WooCommerce 3.4.x
* Fixed bring booking issue after integrating mailbox packages
* Fixed the calculate by weight option
* Fixed shipping calculation for variations

= 1.5.0
* Fixed: BOOK-INPUT-020: Invalid product ID
* Fixed: Pickup point country fixes
* Translations: Updated language files

= 1.4.1-rc1 =
* Added support for Mailbox / Pakke i postkassen
* Added compability with KCO

= 1.4.0.8 =
* Fixed an issue with multipacking that affected cargo and heavy items
* Additional capabilities added when accessing Bring Labels
* Finished writing the readme, updating the FAQ
* Added Mybring.com API authentication check when saving settings
* Fixed an issue where no shipping rates would show up if the max_products setting was empty
* Added an update notice to the description
* Fixed code that was incompatible with php 5.6
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
