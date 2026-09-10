# The two settings options

The plugin keeps its settings in `bring_fraktguiden_for_woocommerce_settings`.
WooCommerce keeps the shipping method settings in
`woocommerce_bring_fraktguiden_settings`. Both options exist at the same time,
and each one has an owner.

## Who owns what

WooCommerce creates `woocommerce_bring_fraktguiden_settings` through
`WC_Settings_API`, for the shipping method id `bring_fraktguiden`. WooCommerce
writes it whenever a shop owner saves the method in a shipping zone. It holds
the fields that the method declares in `init_form_fields()`, and the fields that
WooCommerce adds itself, such as `enabled`.

The plugin owns `bring_fraktguiden_for_woocommerce_settings`. The settings pages
under the Bring Fraktguiden menu write it. `Fraktguiden_Helper::get_option()`
and `Fraktguiden_Helper::update_option()` read and write it. `Settings` and
`SettingsRepository` build on it.

The plugin never writes the WooCommerce option.

## The sync

`BringFraktguiden\Settings\SettingsMigration` copies values one way, from the
WooCommerce option into the plugin option. Nothing copies back.

A third option, `bring_fraktguiden_settings_snapshot`, records the WooCommerce
option as it stood at the last copy. A copy moves only a key whose value differs
from the snapshot. A snapshot that does not exist counts as empty, so the first
copy moves every key.

The copy runs at two moments:

- On the `add_option_` and `update_option_` hooks for the WooCommerce option.
  This carries a shipping zone save into the plugin option at once.
- On the first read of a request, when the WooCommerce option does not match the
  snapshot. This catches a write made while an older plugin version ran.

## Why the delta, and not a full copy

A shop owner who saves the shipping method makes WooCommerce write all of its
keys at once. A full copy would then push every stale WooCommerce value into the
plugin option, and undo the settings saved through the plugin pages. The
snapshot tells the two apart. Only a value that really changed moves.

## Why one way

An older plugin version reads the WooCommerce option and knows nothing about the
plugin option. The WooCommerce option therefore has to keep working settings, so
a shop owner can install an older version and keep selling.

A setting saved through the plugin pages never reaches the WooCommerce option.
That setting is lost if the shop rolls back. A shop that rolls back gets the
settings it had before the upgrade.

## Rules for a new setting

Add a new plugin setting to `config/admin-settings.php` and to `Settings`. Do
not add it to the shipping method form fields. A key written by both owners
collides, because a WooCommerce write always wins on the next copy.
