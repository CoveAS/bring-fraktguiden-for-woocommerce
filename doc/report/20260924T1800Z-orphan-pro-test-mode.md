# The orphan Pro test mode

Version 1.11.7 had a setting `test_mode`, labelled "Enable test mode". The new
settings pages do not show it. The code still reads it.

A site that saved `test_mode = yes` in 1.11.7 stays in Pro test mode. No screen
can turn it off.

## How the value survives

1. `SettingsMigration::copy_forward()` copies every key of
   `woocommerce_bring_fraktguiden_settings` into
   `bring_fraktguiden_for_woocommerce_settings`. The first sync copies
   `test_mode` too.
2. `Fraktguiden_Helper::pro_test_mode()` reads `test_mode` from the plugin
   option. It returns true when `pro_enabled` is also `yes`.
3. No field, no save and no migration step removes the key.

## What the flag does today

`pro_test_mode()` has three readers.

| Reader | Effect |
|---|---|
| `pro/class-wc-shipping-method-bring-pro.php:22` | Loads the pickup points without a license or a trial. |
| `Bring_Fraktguiden::checkout_message()` | Shows every shop customer "Bring Fraktguiden PRO is in test-mode. Deactivate the test-mode to remove this message." on the checkout page. |
| `Fraktguiden_Helper::get_pro_description()` | Builds a notice text. `Fraktguiden_Admin_Notices::init()` computes it, but the notice that uses it is commented out. |

The flag does not touch a booking. `Bring_Booking::is_test_mode()` reads
`booking_test_mode_enabled` and the license. It never reads `test_mode`.

The flag unlocks no other Pro feature. Free shipping limits and fixed prices
read `pro_activated()` only.

## Who it hurts

1. The shop owner. Real customers see a test mode message at checkout. The
   message tells the owner to turn test mode off, but no switch exists.
2. The plugin vendor. A site with an expired trial keeps the pickup points for
   free.

The number of live sites with the flag is unknown. The plugin sends no
settings to the license server. (Genuine uncertainty.)

## What replaced it

The testing license covers the same need. The license server sends
`testing_key`, the license counts as valid, and every booking goes in test
mode. See `doc/testing-license.md`.

So the old flag has no job left.

## Ideas

1. ★ Delete `pro_test_mode()` and its three readers. The pickup point gate
   becomes `pro_activated()`. The checkout message goes away. A site that
   needs a test shop asks for a testing license.
2. Delete as in idea 1, and show one admin notice on a site that still holds
   `test_mode = yes`. The notice says that test mode moved to the testing
   license, and links to the Pro page.
3. Put the checkbox back on the Pro settings page. This keeps a second test
   mode next to the testing license, and a shop can still unlock pickup points
   without paying.
4. Keep the reader and remove the key from the plugin option once. This needs a
   migration step, and a later WooCommerce save of the old settings can copy
   the key back.

Idea 1 removes the fault with deletion only. Idea 2 adds a notice for a group
of unknown size.

## Result

Idea 1 is done. The plugin no longer reads `test_mode`.

## Details for the change

1. Leave the stored `test_mode` value alone. No reader is left, and 1.11.7
   reads `woocommerce_bring_fraktguiden_settings` after a roll back.
2. Remove the `pro_test` state and the `test_mode` keys from
   `Development/StateSelector.php`.
3. `get_pro_description()` has no visible output today. Delete it with the
   unused `$message` line in `Fraktguiden_Admin_Notices::init()`.
4. The `_test_mode` field in `class-fraktguiden-order-debug.php` is order meta
   of a booking. It is not this setting. Keep it.
5. Remove the checkout string from the POT and `.po` files with the normal
   catalog update.
