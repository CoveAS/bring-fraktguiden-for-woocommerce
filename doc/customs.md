# Bring customs data in the Booking API

Bring's Booking API carries customs data in one object, `customsInformation`.
Two separate rules make this plugin need it. This page holds what the two share.

- [NVIT](nvit.md), a transit rule for a domestic Norwegian shipment that passes
  through Sweden or Finland.
- [Export from Norway](export.md), a declaration for a shipment that leaves
  Norway.

Both rules ask for nearly the same data per item line. An export adds the
country of origin. The rest of the request differs more.

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
| `countryCodeOrigin` | string | for an export | The country of origin. |
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

The schema also marks `countryCodeOrigin` as optional, but an export needs it.
An export without it fails with `BOOK-INPUT-028`, "Invalid country code". The
code carries no party name, so it does not say which field is wrong. A transit
booking books without the field.

## The separate Customs API

Bring also runs a Customs API at `https://api.bring.com/customs/v1`. It takes a
full declaration, with invoice data, a declaration type and a trade preference
code. It marks `customsArticleNumber`, `grossWeight`, `netWeight` and
`countryCodeOrigin` as required, which the Booking API does not.

The plugin should not use it. It needs a separate onboarding and a test round
with edi@bring.com, and it serves bulk and routing label flows.

## The state of this plugin

The plugin builds `customsInformation` for both rules.
`CustomsInformation::for_order()` holds it. An NVIT booking carries the top
level `type`, and an export leaves it out. `CustomsParties::for_order()` adds
the `exporter` and the `importer` that an export needs.

This plugin stores the HS code, the goods description, the net weight and the
country of origin in post meta, on a product and on a variation. A variation
uses its own set only when the shop turns on the override checkbox. See
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

## Where the list of HS codes comes from

A shop does not type an HS code. It picks one from a list, and the list holds
the Norwegian customs tariff.

Tolletaten publishes the tariff as open data, under Creative Commons
Attribution 4.0. The file is `tolltariffstruktur.json` on data.toll.no. It holds
the whole tariff as a tree of sections, chapters, positions and goods.

The plugin fetches that file, keeps one row per six digit code, and holds the
result in a transient for a month. That gives 4587 codes and about 550 kB. The
browser reads the rows from the REST route `bring-fraktguiden/v1/hs-codes`, and
keeps them in localStorage, so a shop downloads the tariff once.

The tariff names goods down to eight digits. The first six are the
international HS code, and customs takes those six from an exporter. So the
list stops at six.

data.toll.no sends no CORS header, so the browser cannot read the file itself.
WordPress fetches it.

The tariff changes on 1 January each year. The month long transient picks the
change up without a plugin release.

Bring runs its own HS code search at `/checkout/customs/hscode`. It is not
documented, and it needs the onboarding token of Posten Bring Checkout, which
this plugin does not hold.

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
- [Tolletaten open data, the tariff structure](https://data.toll.no/dataset/tolltariffstruktur)
