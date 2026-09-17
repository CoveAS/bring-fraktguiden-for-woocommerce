# The testing license

The license server sends `testing_key` in the answer of a license check. A
testing license unlocks every Pro feature. It may never book a real shipment.

## What the plugin does with the flag

`Fraktguiden_License::store_answer()` keeps the flag in the license state
option, and writes `booking_test_mode_enabled` to `yes` when the flag is true.
`Fraktguiden_License::is_testing()` reads the flag back.

The setting stays a plain setting, so every reader of
`booking_test_mode_enabled` follows the license without a change.

## What holds the setting down

The shop owner can still post the Booking form. `SettingsPage::process_settings()`
therefore writes `yes` again on every save while the license is a testing one.
The checkbox on the Booking page carries the `disabled` attribute in the same
case. A notice under the checkbox names the license as the reason and links to
the Pro page.

## Where a booking reads it

`Bring_Booking::is_test_mode()` is the one reader. The consignment request sends
its answer as `testIndicator`, and Mybring then drops the shipment after the
booking. The order screen and the label view mark the order as test mode.

## The license server is not always there

A check that fails leaves the old state, so a shop keeps test mode until a
later check says otherwise. A shop never gains live booking from a timeout.
