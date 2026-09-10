# Bring customs data in the Booking API

Bring's Booking API carries customs data in one object, `customsInformation`.
Two separate rules make this plugin need it. This page holds what the two share.

- [NVIT](nvit.md), a transit rule for a domestic Norwegian shipment that passes
  through Sweden or Finland.
- [Export from Norway](export.md), a declaration for a shipment that leaves
  Norway.

Both rules ask for the same data per item line. The rest of the request differs.

## Where the data goes

The Booking API carries both the transit data and the export data. The base URL
is `https://api.bring.com/booking`. The OpenAPI document is at
`https://api.bring.com/booking/api-docs`.

The data goes in the `customsInformation` object. That object sits on each entry
of the `consignments` array. It replaces the older `ediCustomsInformation`
element, which Bring marks as deprecated.

Set the top level `type` to `NVIT` to mark the data as transit data. Leave it
out for an export.

### Fields on `customsInformation`

| Field | Type | Notes |
|---|---|---|
| `type` | string | Set to `NVIT` for transit data. |
| `customsDeclarations` | array of objects | One entry per item line. Marked required. |
| `consent` | boolean | Marked required. Must be `true`. Required for an export. |
| `natureOfCargo` | object | Marked required. Required for an export. |
| `ioss` | string | An IOSS number for an export from Norway to the EU. |
| `voec` | string | A VOEC number for an import to Norway. |

### Fields on each `customsDeclarations` entry

| Field | Type | Required | Maps to |
|---|---|---|---|
| `amount` | number | yes | The value of the line, including VAT. |
| `currency` | string | yes | The currency code for `amount`. |
| `goodsDescription` | string | yes | The description of the goods. |
| `customsArticleNumber` | string | no | The HS code. Length 6 to 10. |
| `grossWeight` | number | no | The weight in kg, with the packaging. |
| `netWeight` | number | no | The weight in kg, without the packaging. |
| `countryCodeOrigin` | string | no | The country of origin. |
| `numberOfPieces` | integer | no | The number of declared pieces. |
| `quantity` | integer | no | Deprecated. |

The schema sets no length limit on `goodsDescription`. A test booking confirms
it. The API accepted a description of 20000 characters and returned a
consignment number.

The `goodsDescription` on a package is a different field, and that one has a
limit of 35 characters. The two fields do not share a rule.

The schema marks `customsArticleNumber`, `grossWeight` and `netWeight` as
optional. Both rules still need all three, so treat them as required. Bring's
separate Customs API does mark them required.

## The separate Customs API

Bring also runs a Customs API at `https://api.bring.com/customs/v1`. It takes a
full declaration, with invoice data, a declaration type and a trade preference
code. It marks `customsArticleNumber`, `grossWeight`, `netWeight` and
`countryCodeOrigin` as required, which the Booking API does not.

The plugin should not use it. It needs a separate onboarding and a test round
with edi@bring.com, and it serves bulk and routing label flows.

## The state of this plugin

The plugin sends no customs data. `create_consignment()` in
[class-bring-booking-consignment-request.php:241](../pro/booking/consignment-request/class-bring-booking-consignment-request.php#L241)
sets `customsDeclaration` to `null` inside the `product` object. That element
name is not the current one, and Bring deprecated the whole older customs
structure.

The plugin also builds only `sender` and `recipient` in `parties`, so an export
booking has no `exporter` and no `importer`.

This plugin stores the HS code, the goods description and the net weight in post
meta, on a product and on a variation. A variation uses its own set only when
the shop turns on the override checkbox. See
[Posten Bring Checkout](posten-bring-checkout-nvit.md) for the attribute that
this plugin reads as a fallback.

WooCommerce has no native field for an HS code. It stores a line total without
tax, so the VAT needs to be added back.

One HS code field serves both rules, because the item line fields are the same.

## The HS code notation

Tolltariffen prints an HS code with dots, for example 3305.10.00. Customs takes
the plain digits, and the Booking API counts characters for its length rule of 6
to 10. A code with dots therefore does not fit.

This plugin stores and sends the digits only. It drops a code that holds fewer
than 6 or more than 10 digits, because such a code names no goods.

## The two weights

The gross weight is the goods with their packing. The net weight is the goods
alone. Both exclude the equipment of the carrier, such as a pallet. The World
Customs Organization sets these definitions, and Norwegian customs follows them.

WooCommerce stores one weight per product. A shop enters what the parcel scale
shows, so that weight is the gross weight. This plugin adds a net weight field
per product and per variation, and falls back to the WooCommerce weight when the
field is empty.

Both weights are per item line, so the plugin multiplies the unit weight by the
quantity of the line. The plugin converts the weight to kilograms, because a
shop may set another weight unit.

## Sources

- [Bring Developer, Customs information](https://developer.bring.com/api/booking/customs/)
- [Bring Developer, Booking API](https://developer.bring.com/api/booking/)
- [Bring Developer, Customs API](https://developer.bring.com/api/customs/)
- [Bring Developer, API service portfolio](https://developer.bring.com/api/services/)
- [Tolltariffen, look up an HS code](http://tolltariffen.toll.no/)
