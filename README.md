# Bring Fraktguiden 🚛
#### Bring shipping for Woocommerce

Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no

All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

**Bring Fraktguiden now requires you to have a [Mybring.com](https://www.mybring.com) account linked to your store. If updating from 1.5, please note that your shipping options won't show until you enter your email and API key into the Bring Fraktguiden settings.**

Please consult the installation section and the changelog section before installing or upgrading.

> Special thanks goes to out to **Matt Gates** for starting this project and allowing us to build upon his original codebase.

## Installation guide

When you install bring, you need to head to the settings page to start configuring Bring to your specifications.

#### Installing and activating
1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory, or install through wordpress plugins.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin > WooCommerce > Settings > Shipping
4. Select **Bring Fraktguiden**
5. Enable and configure Bring Fraktguiden

#### Setting up Woocommerce

To ensure Bring is working correctly, you must set the following:

1. **Dimension and weight units**
	* WooCommerce > Settings > Products
2. **Currency**
	* WooCommerce > Settings > General
3. **Product dimentions**
	* Woocommerce > Products > Product

## Frequently Asked Questions

##### Why do rates not show up on the cart page?
Bring rates are only shown when the customer has entered a valid postcode. Commonly customers have either entered the wrong postcode or live outside of your Bring postcode settings.

##### But the postcode is valid and the rate still isn't showing?
If you've entered any Mybring details, try removing them. If Bring shows up on the cart page after they're removed, it suggests that your details may be incorrect. If it's still not showing after that, you should check that all the settings are correctly filled out.

##### What do I get from Bring Pro that I can't from the Free version?
Good question! Bring Pro enables you to customise your customers Bring experience:

- You can customise the price of each shipping method
- You can set targets that enable free shipping if your customers spend a certain amount ( For example, Free shipping when you spend 1000 ). 
- You can book your shippment through Mybring directly on your orders page.
- You can change the services offered to customers to their nearest collection point

##### 'Minipakke' and 'Småpakke' are no longer available? What happened?
These have been replaced with new and exciting shipping option called 'Pakke i postkassen'! You can read more about it here: https://www.bring.no/radgivning/netthandel/pakkeipostkassen. In essence, it's  a streamlined version of the previous options available.

##### Where are 'A-Mail' and 'B-Mail' options?
Bring revealed that in 2018, A-mail and B-mail have been merged into a new service called "Brev". The idea is that we just don't post as much as we used to due to the digitalisation of mail. Combining A & B Mail means that daily flights to deliver post goes from 10 flights per day to 2 flights. Pretty neat right?

##### My products have dimensions, but they're being classified as heavy, why?
Check your dimensions are set to the right unit values. Often, products are measured in MM but the default woocommerce unit is CM. This causes Bring to consider your products to be 10x their size.

##### I activate the plugin and everything goes white!
A rare occasion, but when this happens, it means that your server is running an old version of PHP – You can find out what version of PHP you're using by heading to **Woocommerce -> Status**, this will let you know if you're running out-of-date software. The best way to recover from this [WSOD](https://codex.wordpress.org/Common_WordPress_Errors#The_White_Screen_of_Death) is to use the FileEditor, PHPMyadmin, sFTP or SSH to rename the plugin, thus disabling it in wordpress. You should then consider upgrading your PHP version by contacting your Host provider.

##### What kind of support do you provide?
We monitor the Support forum of this plugin frequently to assist you in running your shop smoothly. You can visit the [Support](https://wordpress.org/support/plugin/bring-fraktguiden-for-woocommerce) section to read about any ongoing issues, or you can [Submit a new issue](https://wordpress.org/support/plugin/bring-fraktguiden-for-woocommerce#new-post) if you've discovered a problem.

##### Do you have a github where I can contribute?
Yes, yes we do. [Bring Github](https://github.com/drivdigital/bring-fraktguiden-for-woocommerce). Please make pull requests to the Develop branch. Pull the develop branch, make any changes you see fit & create a PR.

---

### Changelog

* Updated translations
* Updated PDFMerger from 1.0 to 2.0
* Updated plugin URL to be relevant to Bring Fraktguiden
* Fixed filter stopping settings link from showing on plugin list

= 1.6.1 =

* Added admin notice for required Mybring API credentials

= 1.6.0 =

* Added support for multiple Customer Number
* Added Customer Number selection when booking orders
* Added additional postcode validation for Norway
* Added support for additional language translations
* Updated php-laff from 1.0 to 1.1
* Updated Bring API version
* Updated formatting to align with coding standards

= 1.5.13 =

* Fixed not allowed to access this page issue when creating labels

= 1.5.12 =

* Fixed a problem where the KCO support script used data from the pickupoint API
* Disabled enhanced KCO support by default for newer versions of KCO
* Fixed a bug where tracking code would appear on order confirmation emails even if there was no tracking on the order

= 1.5.11 =

* Fixed mailbox tracking code url
* Fixed an issue causing settings to be lost when turning off PRO
* Made some text changes
* Added a redirect to the settings page when there are empty store address fields when booking

= 1.5.10 =

* Fixed an issue with not being able to book with mailbox

= 1.5.9 =

* Fixed an issue with changing the shipping item for booking in admin
* Fixed tracking number not showing in emails
* Fixed some php 7.2 warnings

= 1.5.8 =

* Improved adding shipping items to orders from admin

= 1.5.7 =

* Fixed a 500 caused by php 7.0 syntax on older php versions

= 1.5.6 =

* Fixed a 500 error when trying to book with bring with shipping items that were not added through checkout

= 1.5.5 =

* Fixed editing pickup point on orders in wp-admin

= 1.5.4 =

* Fixed issue with label downloads

= 1.5.3 =

* Fixed free shipping
* Removed VAT setting
* Fixed some minor php 7.x issues
* Fixed tax calculation for fixed prices
* Fixed logic for heavy items

= 1.5.2 =

* Added compability with WooCommerce 3.4.x
* Fixed bring booking issue after integrating mailbox packages
* Fixed the calculate by weight option
* Fixed shipping calculation for variations

= 1.5.0 =

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
* Mybring Booking has been added to the PRO version, which allows you to now book your orders with Bring directly on the order page.
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

---

## Contributing

To file an issue, use this repo: https://github.com/drivdigital/bring-fraktguiden-for-woocommerce/issues/new

### How to Contribute

Bring Fraktguiden for WooCommerce is an open source project and we love pull requests and feedback from anyone. By participating in this project, you agree to abide by the Covenant [code of conduct](http://contributor-covenant.org/version/1/4/)

### Create a Ticket

* Make sure you have a [GitHub account](https://github.com/signup/free)
* [Submit a ticket for your issue](https://github.com/drivdigital/bring-fraktguiden-for-woocommerce/issues/new), also check if your issue hasn't already been reported.
  * Clearly describe the issue including steps to reproduce when it is a bug.
  * Make sure you fill in the earliest version that you know has the issue.

### Make Changes

* [Fork](https://help.github.com/articles/fork-a-repo/) the project.
* Create a topic branch from the master branch.
	* To quickly create a topic branch based on master; `git checkout -b
	fix/master/my-contribution master`. Please avoid working directly on the
	`master` branch.
* Make changes to your forked repository
	* Ensure you stick to the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
	* Ensure you use LF line endings
* Make sure you have tested your changes.
* Make commits of logical units.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Make sure your commit messages are in the proper format.

````
$ git commit -m "A brief summary of the commit
>
> A paragraph describing what changed and its impact."
````
* Create a [pull request](https://help.github.com/articles/using-pull-requests/)
