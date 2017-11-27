# Bring Fraktguiden

Bring Fraktguiden provides shipping calculations based on shipping rates from bring.no.

All standard shipping methods from Bring are built-in. Volume calculations based on product dimensions and weight.

Please consult the installation section and the changelog section before installing or upgrading.

> Special thanks goes to out to **Matt Gates** for starting this project and allowing us to build upon his original codebase.

If you want to contribute or file an issue: https://github.com/drivdigital/bring-fraktguiden-for-woocommerce

## Installation guide

When you install bring, you need to head to the settings page to start configuring Bring to your specifications.

Firstly, make sure WooCommerce is configured with:

* Dimension unit and weight units (see WooCommerce > Settings > Products)
* Currency (see WooCommerce > Settings > General)
* Product dimentions (All products must have sizes and weights set – without this the calculation cannot be performed)

1. Upload `bring-fraktguiden-for-woocommerce` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin > WooCommerce > Settings > Shipping
4. Select **Bring Fraktguiden**
5. Enable and configure Bring Fraktguiden


== Frequently Asked Questions ==

**Q: Why do rates not show up on the cart page?**
A: Rates are only shown when the customer has a valid postcode/zip code.

**Q: My products have dimensions, but they're being classified as heavy, why?**
A: Check your dimensions are set to the right unit values. Often, products are measured in MM but the default woocommerce unit is CM. This causes Bring to consider your products to be 10x their size.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md)