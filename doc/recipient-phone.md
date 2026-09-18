# The recipient phone number

Bring reaches the recipient by phone on some services. Pakkeboks sends the
locker code by SMS, so a Pakkeboks parcel with no number cannot be collected.
The Pakke hjem pluss driver rings the recipient 30 to 60 minutes before an
evening delivery.

## The rule in this plugin

A service that needs a number carries `requires_phone => true` in
`config/services.php`. 5801 Pakkeboks and 5600 Pakke hjem pluss carry it
today.

`BringFraktguiden\Checkout\PhoneRequirement` reads the flag. The checkout
refuses the order when the customer chose such a service and gave no usable
number.

A service without the flag needs no number. An unknown service therefore stops
no order.

## Which number

The class checks the billing phone, because
`Bring_Booking_Consignment_Request::get_recipient_address()` sends the billing
phone to the booking API.

A number counts as usable when it holds at least 8 digits. Every other
character is ignored, so `+47 999 99 999` passes.

## Both checkouts

The classic checkout runs `woocommerce_after_checkout_validation`. The block
checkout runs `woocommerce_blocks_validate_location_address_fields` instead,
once for the billing address and once for the shipping address. The class acts
on the billing group only.

Both hooks take a `WP_Error` keyed by field id, so the error lands on the phone
field.

The check runs on the server, so the customer reads the error after they press
Place order. Nothing marks the field as required before that.

## Which service the customer chose

The chosen rate carries its Bring product in rate meta, under `bring_product`.
`WC_Shipping_Method_Bring::push_rate()` always sets it.

Do not read the product out of the rate id. A pickup point rate appends the
point id, so the id reads `bring_fraktguiden:5801-12345`.
