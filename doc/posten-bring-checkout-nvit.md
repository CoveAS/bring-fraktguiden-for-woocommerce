# How Posten Bring Checkout handles NVIT

Bring ships a second WooCommerce plugin, [Posten Bring
Checkout](https://wordpress.org/plugins/posten-bring-checkout/). It already
sends NVIT data. See [NVIT](nvit.md) for the rule itself and
[customs data](customs.md) for the Booking API fields.

This page records where that plugin keeps its data. A shop that moves from that
plugin to ours must keep its HS codes. So our plugin reads and writes the same
places, with the same names.

## Where the HS code lives

The HS code is a WooCommerce product attribute. It is not post meta.

The plugin accepts four attribute slugs, in this order:

1. `hscode`
2. `htscode`
3. `hs-code`
4. `hts-code`

Each slug becomes a taxonomy through `wc_attribute_taxonomy_name()`. The slug
`hscode` gives the taxonomy `pa_hscode`. The first slug that holds a value wins.

The term name is the HS code itself, for example `330510`. The plugin reads the
term name, never the term slug.

## The plugin creates the attribute on demand

A fresh install has no `pa_hscode` attribute. Nothing appears on the product edit
screen until a shop admin saves a first HS code.

On that first save the plugin looks for the four slugs above. If none exists, it
creates one:

| Property | Value |
|---|---|
| Slug | `hscode` |
| Label | `HS Code` |
| Type | `select` |
| Order by | `name` |
| Archives | off |

The plugin then sets the attribute on the product with `set_visible( false )` and
`set_variation( false )`. The code stays out of the shop front.

A shop admin may also create the attribute by hand, with any of the four slugs,
and fill it per product. The plugin reads that too.

## A variation reads its own value first

For a product variation the plugin reads the post meta `attribute_pa_hscode` on
the variation. That meta holds a term slug. The plugin then looks up the term and
returns its name.

If the variation holds no value, the plugin falls back to
`$product->get_attribute()`, which returns the parent value.

On save the plugin writes a variation differently from a simple product. A
variation gets the term slug in its attributes array. A simple product gets a
`WC_Product_Attribute` object.

## When the plugin asks for a code

The plugin does not guess the postal code ranges. It asks Bring.

The call is `GET /checkout/customs/nvit?fromPostalCode=&toPostalCode=` on the
Mybring API. The answer holds `requiresHsCode`, a boolean. The plugin caches the
answer in a transient for 480 seconds. The transient key is
`posten_bring_checkout_nvit_` plus an MD5 of the two postal codes.

The plugin only makes this call when the sender country and the recipient country
are both `NO`. The sender postal code comes from the WooCommerce store address.
Any error, or any status other than 200, counts as "not needed".

## How a shop admin fills in a code

The prompt sits on the order edit screen, not on the product edit screen.

1. The order screen shows a notice: "N items require an HS code".
2. A button opens a modal named "NVIT - Items missing HS code".
3. Each line without a code gets a search box. One box at the top sets the same
   code for every line.
4. The admin types at least 3 characters, as a code or as a word.
5. The plugin calls `GET /checkout/customs/hscode?q=` on the Mybring API. It
   sends `Accept-Language` from the site locale, or from WPML or Polylang. The
   answer is cached for 60 seconds.
6. The list shows the code in the dotted form `33.05.10` with the description.
7. On save the browser posts to the REST route
   `/posten-bring-checkout/hscodes`. The body is an array of
   `{ productId, hsCode }`. The `productId` is the variation id for a variation.

The plugin skips a product whose stored code already matches. So the same product
is never asked for twice.

## What the plugin sends per item line

The plugin builds one `customsDeclarations` entry per order line of type
`line_item`. It sets `type` to `NVIT` on `customsInformation`.

| Field | Source |
|---|---|
| `amount` | `$item->get_total()` |
| `currency` | The order currency |
| `goodsDescription` | The product name, then `. `, then the short description |
| `customsArticleNumber` | The HS code attribute |
| `netWeight` | The product weight in kg |
| `grossWeight` | A copy of `netWeight` |
| `quantity` | The order line quantity |
| `countryCodeOrigin` | Not set for NVIT |

`consent` is always `true`. `natureOfCargo.type` is always `SALE_OF_GOODS`.

## Where their values are wrong

Copy their field names. Do not copy these values.

1. `amount` excludes VAT. `get_total()` is the line total before tax. Bring wants
   the value with VAT.
2. `grossWeight` equals `netWeight`. No packaging weight is added.
3. `numberOfPieces` is never set. They send the deprecated `quantity` instead.
4. `voec` and `ioss` exist on their class but nothing sets them.
5. `goodsDescription` has no length limit and no fallback for an empty short
   description.

## What our plugin must do to stay compatible

- Read the four slugs in the same order, through the same taxonomy names.
- Store the HS code as the term name, not as the term slug.
- Read a variation from `attribute_pa_hscode` post meta first, then fall back to
  the parent.
- Create the attribute with the slug `hscode` when none of the four exists.
- Do not require the shop to re-enter a code that their plugin already saved.
