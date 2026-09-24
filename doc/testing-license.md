# The testing license

The license server sends `testing_key` in the answer of a license check. A
testing license unlocks every Pro feature. It may never book a real shipment.

## What the plugin does with the flag

`Fraktguiden_License::store_answer()` keeps the flag in the license state
option. `Fraktguiden_License::is_testing()` reads the flag back.

The plugin never writes `booking_test_mode_enabled` for a testing license. The
stored value is the choice of the shop owner, and it applies again when the
license stops being a testing one.

## Where a booking reads it

`Bring_Booking::is_test_mode()` is the one reader. It returns true for a testing
license, whatever `booking_test_mode_enabled` holds. The consignment request
sends its answer as `testIndicator`, and Mybring then drops the shipment after
the booking. The order screen and the label view mark the order as test mode.

## The Booking page

The checkbox on the Booking page shows the mode that bookings use. For a
testing license it is checked and carries the `disabled` attribute, whatever the
stored value is. A notice under the checkbox names the license as the reason and
links to the Pro page.

A disabled checkbox posts nothing. `SettingsPage::process_settings()` therefore
skips the field while the license is a testing one, so a save keeps the stored
value.

## The license server is not always there

A check that fails leaves the old state, so a shop keeps test mode until a
later check says otherwise. A shop never gains live booking from a timeout.
