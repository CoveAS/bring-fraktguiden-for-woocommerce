# Bring Fraktguiden 🚛
#### Bring shipping for Woocommerce

Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no

All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

Please consult the installation section and the changelog section before installing or upgrading.

> Special thanks goes to out to **Matt Gates** for starting this project and allowing us to build upon his original codebase.

## Installation guide

When you install bring, you need to head to the settings page to start configuring Bring to your specifications.

#### Installing and activating
1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory
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

**Q: Why do rates not show up on the cart page?**

A: Rates are only shown when the customer has a valid postcode/zip code.

**Q: My products have dimensions, but they're being classified as heavy, why?**

A: Check your dimensions are set to the right unit values. Often, products are measured in MM but the default woocommerce unit is CM. This causes Bring to consider your products to be 10x their size.

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

### Create a Release

This section applies to project maintainers only.

**Prerequsites:**

* [Node.js](https://nodejs.org)
* [driv-cli](https://bitbucket.org/drivdigital/driv-cli)
* [SVN](https://tortoisesvn.net/) (for commiting to Wordpress.org)
* Write access to the Wordpress.org SVN repository

**Procedure:**

* Make sure you have SVN installed and have read/write access to the WordPress.org project
* Install [driv-cli](https://bitbucket.org/drivdigital/driv-cli)
    * Add `git@bitbucket.org:drivdigital/driv-cli-wp.git` to your `${DRIV-CLI-HOME}/repositories.json`
* Navigate to `${PROJECT-HOME}`.
* Make sure the `build.json` has the correct version number.
* Type `driv wp plugin` (assuming you have created an alias named `driv`).
* Choose `release` from the menu.