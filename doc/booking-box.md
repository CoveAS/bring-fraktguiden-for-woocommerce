# The Bring booking box

The box on the order screen turns one WooCommerce order into one Bring
consignment. It lives in `pro/booking/box/`, and its markup lives in
`src/templates/admin/booking/`.

## One order, one consignment

A WooCommerce order records no link between a product line and a shipping line,
so nothing says which goods travel on which shipment. The box therefore books
the whole order as one consignment, whatever number of Bring shipping lines the
order carries. It writes the product and the parcels onto the first Bring
shipping line, because `Bring_Booking_Consignment_Request` reads them from
there.

`Bring_Booking::send_booking()` still loops over every Bring shipping line. The
bulk action on the orders list uses it. The box does not.

## One form, no steps

The box opens filled and ready to book. A shop worker never presses a start
button and the page never reloads to reach a second step.

## The draft

What the worker types is saved to `_bring_booking_draft` on the order, so a
reload keeps the edits. The draft never goes stale on its own. The Reset button
clears it, and the form fills from the order again.

Nothing but the draft is written before a booking. The product and the parcels
reach the shipping line at the moment of the send.

## The history

Every attempt appends to `_bring_booking_responses`, oldest first. A failed
attempt is recorded too, so the reason survives a reload.

The newest attempt also writes `_bring_booking_response`, the key the plugin
used before it kept a history. The label download, the orders list column and
the debug screen read that key and need no change.

An order booked before the history existed has only the old key. The box reads
it as a history of one.

## Booking again

A consignment can fail, go to the wrong Mybring account, or be cancelled in
Mybring. So a booked order offers a Book again button, which opens the form
filled from the last attempt.

The plugin cancels nothing at Bring. A shop worker does that in Mybring.

## Two sends of one form

The form carries a one use token, held in `_bring_booking_token`. A booking
spends it. A repeat send carries a spent token and is refused. Opening the box
issues a new token.

## The route

The browser never builds the form. `BookingRoute` takes the form as JSON at
`POST /bring-fraktguiden/v1/orders/<id>/booking`, and an answer that changes the
form carries the fresh markup of the whole box. So one render path serves the
first paint and every redraw.

The route registers outside the `is_admin()` check, because a REST request is
not an admin request.

An `action` field says what to do with the form: `save` keeps the draft, `reload`
keeps the draft and redraws, `reset` throws the draft away, `book` sends the form
to Bring, and `form` opens the form on a booked order.

## The silent save

A `save` answer carries no markup. The shop worker already holds the true form,
so a redraw would only take the caret, the open dropdown and the scroll
position away in the middle of the typing.

A `reload` does redraw, because a new service brings other extra services and
other fields, which only the server knows.

A failed save shows a line above the buttons and turns the Book button off. The
next save that works clears both. A booking of an unsaved form is refused,
because the shop worker cannot see what the order now holds.

A booking waits for the save in flight. A booking clears the draft, and a save
that lands after it would write a draft back onto a booked order.

## Values that used to come from $_POST

`Bring_Booking_Consignment_Request` read the booking form straight out of
`$_POST`. The box sends JSON, so the request now takes the same values through
`fill()`: `additional_services`, `additional_info_sender`,
`additional_info_recipient` and `nature_of_cargo`. A null value still reads
`$_POST`, which is what the bulk booking does.
