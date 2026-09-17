# Return labels

A return sends goods from the customer back to the shop. Bring sells a return
as its own service, with its own service code. A return is never a flag on an
outbound service.

## The two ways to make a return label

Bring offers two ways.

1. **The return rides along with the outbound booking.** The Booking API takes a
   `returnProduct` element next to `product`. The answer holds both labels. Bring
   invoices the return only when the customer uses it. This works for parcel
   services inside Norway.
2. **The return is booked on its own.** The shop sends a booking request with a
   return service as the product. The sender is the customer and the receiver is
   the shop.

Source: https://developer.bring.com/api/booking/free-return/

### Which pairs the ride along way accepts

Outbound services: 3584, 4850, 5000, 5600, 5800.

Return services: 9000, 9300, 9350, 9600.

## Service codes

### Norway, domestic

| Code | Name | Who returns |
|---|---|---|
| 9300 | Retur fra hentested | Private customer |
| 9350 | Retur til bedrift | Private customer |
| 9000 | Return business parcel | Business |
| 9600 | Return express | Business |
| 9100 | Return business groupage | Business, cargo |

9300 and 9350 both take the parcel from a private customer. The customer hands
it in at a post office, a parcel box or their own mailbox.

The two differ in where the return lands. 9350 ends at the shop address, driven
by Bring. 9300 waits at the pick-up point nearest the shop.

### Nordic and international parcel

| Code | Name | Countries |
|---|---|---|
| 0341 | PickUp Parcel Return | NO, SE, DK, FI |
| 0343 | PickUp Parcel Return Bulk | NO, SE, DK, FI, and parts of Europe |
| 0331 | Business Parcel Return | NO, SE, DK, FI |
| 0333 | Business Parcel Return Bulk | NO, SE, DK, FI |

A bulk service clears customs for many parcels at once.

PickUp Parcel Return does not cover a return inside Finland.

### Home delivery return

| Code | Name | Countries |
|---|---|---|
| 2778 | Home Delivery Return | NO, SE |
| 3578 | Indoor Return | NO, SE |
| 3577 | Curbside Return | NO, SE |

Source: https://developer.bring.com/api/services/

## The mailbox return is not in the API

Bring sells "Retur Pakke i postkassen" as service 9650 on bring.no. The customer
leaves the parcel in their own mailbox.

The service portfolio at developer.bring.com does not list 9650, and the free
return page does not pair it with an outbound booking. So the plugin cannot
offer it.

Service 3584, "Pakke hjem i postkassen", is an outbound service, not a return.
Its ride along return travels as 9300 or 9350, from a pick-up point.

## The label free code

Bring can replace a printed label with a code. The code looks like
`BRING-1234-5678`. The customer writes the code, the receiver name and the
receiver address on the parcel.

Bring makes the code on its own when the booking asks for a QR code. This holds
for 9300, 9350, 0341 and 0343 in Norway.

Sweden and Denmark need the extra service 1288 instead. The customer must hand
the parcel in at a pick-up point.

Bring mails the code to the sender and to the receiver. Put both e-mail
addresses in the booking request, or no mail goes out.

Source: https://developer.bring.com/api/booking/labels/

## Limits

A parcel return carries up to 35 kg per parcel in Norway. The Swedish page for
PickUp Parcel Return states 20 kg. The longest side is 150 cm. The length plus
the girth stays under 300 cm.

A groupage return carries up to 3500 kg per shipment. A pallet return carries up
to 750 kg per pallet.

## The price

A return service has no price in the Shipping Guide answer. Bring prices a
return by the agreement of the shop. Ask Bring for a price.

## What the shop must arrange first

Bring needs a return agreement before it accepts a return booking. The Nordic
return services carry a "Return setup" step in the Bring portfolio. The shop
pays for the return, not the customer.

## Sources

- https://developer.bring.com/api/services/
- https://developer.bring.com/api/booking/free-return/
- https://developer.bring.com/api/booking/labels/
- https://www.bring.no/tjenester/pakker-og-gods/retur
- https://bring.no/tjenester/retur/retur-internasjonalt
- https://www.bring.se/en/services/parcels-and-cargo/returns/consumers/pickup-parcel-return

## What this plugin does

The plugin sends `returnProduct` with the outbound booking. The Booking page
holds one radio group, "Return label", stored as `booking_return_service`. The
value is `none`, `9300`, `9350`, `9000` or `9600`.

A booking carries one `returnProduct`, so a shop picks one return service. The
control is a radio group because each service needs a description. The code and
the name alone do not say where the return lands, or whether it serves a private
or a business customer.

The setting names the five outbound services that carry a return label, so a
shop sees that the feature covers Norway only.
`ReturnLabel::product_names()` builds that list from `config/services.php`.

`BringFraktguiden\Booking\ReturnLabel` reads the setting. It returns the return
service only when the outbound service carries `'return_label' => true` in
`config/services.php`.

### Where the return label shows

Bring answers a booking that carried a `returnProduct` with a second
consignment number under `returnConsignmentNumber`, and a second label under
`returnLinks`.

`Bring_Consignment::create_returns_from_response()` reads those two fields. The
booking box then offers a second button, "Print the return label".

The return stays out of `create_from_response()`. That list feeds the customer
facing order view, which prints every consignment as a tracking number. A
customer tracks the parcel they wait for, not a return they may never send.

The plugin books no return on its own. A return outside Norway needs a separate
booking, a reversed address and its own customer number.
