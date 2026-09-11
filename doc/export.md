# Export from Norway

See [Bring customs data](customs.md) for the `customsInformation` object and the
item line fields that an export shares with [NVIT](nvit.md).

A shipment that leaves Norway is an export, not transit. It needs a customs
declaration of its own. The NVIT rule does not cover it, and the `NVIT` type
does not work for it.

Bring requires customs data in the booking for these services, from Norway to
any other country:

- Business Parcel (0330)
- PickUp Parcel (0340)
- Letter Packet (3639)

Both 0330 and 0340 carry parcels from Norway to Sweden, so a shop that ships to
Sweden on either service falls under this rule.

The plugin offers 0330 and 0340. See [config/services.php:152](../config/services.php#L152)
and [config/services.php:249](../config/services.php#L249). The plugin does not
offer 3639.

Bring changed the customs structure in March 2025. The rule took effect at once
for 3639. Bring gave the other services a grace period and said it would give
notice before the period ends.

## What an export adds on top of NVIT

The item line fields are the same. The request differs in four ways.

1. `consent` must be `true`. The sender confirms that the customs data is
   correct and complete, and that the goods are not dangerous or prohibited.
   Bring refuses the booking without it. The consent produces a digital
   signature on the CN23 label.
2. `natureOfCargo.type` is required. It says why the goods move. The values are
   `SALE_OF_GOODS`, `RETURNED_GOODS`, `GIFT`, `COMMERCIAL_SAMPLE`, `DOCUMENTS`
   and `OTHER`. `OTHER` also needs `natureOfCargo.detail`.
3. `parties.exporter` and `parties.importer` are required. Bring's page states
   this, although the schema does not mark them required.
4. Do not set the top level `type` to `NVIT`.

Each party needs a `name`, an `addressLine`, a `city`, a `postalCode` and a
`countryCode`. Both may carry a `vatNumber`, at most 30 characters. Bring
accepts the same data for the exporter as for the sender, and the same data for
the importer as for the recipient, when they do not differ.

`CustomsParties` builds the exporter and the importer. It reads the booking
address of the shop for the exporter, and the shipping address of the order for
the importer. Both carry an address only, because the contact, the reference and
the additional address info belong to the sender party and the recipient party.

## An export request

Bring's own "Brevpakke Utland IOSS" example, cut down to the customs part:

```json
{
  "consignments": [
    {
      "customsInformation": {
        "consent": true,
        "natureOfCargo": { "type": "SALE_OF_GOODS" },
        "ioss": "IM0123456789",
        "customsDeclarations": [
          {
            "amount": 100,
            "currency": "NOK",
            "customsArticleNumber": "123456",
            "goodsDescription": "shampoo",
            "grossWeight": 1,
            "netWeight": 1,
            "countryCodeOrigin": "NO",
            "numberOfPieces": 4
          }
        ]
      },
      "parties": {
        "exporter": {
          "name": "Demo Exporter",
          "addressLine": "Demo exporter address line 1",
          "city": "OSLO",
          "postalCode": "0259",
          "countryCode": "NO"
        },
        "importer": {
          "name": "Demo Importer",
          "addressLine": "Demo importer address line 1",
          "city": "Kobenhavn K",
          "postalCode": "1002",
          "countryCode": "DK",
          "vatNumber": "12345678"
        },
        "recipient": {}
      }
    }
  ],
  "schemaVersion": 1
}
```

## IOSS

`ioss` holds an Import One-Stop Shop number for an export from Norway to the EU.
The format is `IM` and 10 digits. It is only valid when `natureOfCargo.type` is
`SALE_OF_GOODS`. It cannot be combined with a `reference` field on the sender
party.

## The EU handling fee from 3 July 2026

The EU now charges 3 euros per item line on goods that a company outside the EU
sends to a private person inside the EU. Bring reports one effect on booking.
For 0340 and 3639 from Norway, a shipment with IOSS to Belgium, Denmark,
Finland, France, Portugal, Germany, Austria or Luxembourg can only be bought
prepaid on posten.no.

## The exporter number

The setting holds a VAT number or an EORI number. The booking sends it as
`parties.exporter.vatNumber`, because the party holds no other number field.
Bring takes at most 30 characters.

## How the plugin marks a service

A service that needs export customs data carries `'customs' => true` in
`config/services.php`. `ExportRule` reads the flag, and it also checks that the
sender is in Norway and the recipient is not.

## The warning on the order screen

The booking box warns when an order needs customs data and lacks it.
`CustomsWarning` holds the problems, and the markup lives in
`src/templates/admin/parts/customs-warning.bfg.php`.

The warning never stops a booking. Bring holds the guard, and answers with the
reason when it refuses. `ShopCheck` reads the shop settings, and
`ShopProblem` names each one that is empty.

The exporter number is the setting `customs_exporter_number`, on the Booking
settings page. An empty number still books, because the warning never stops a
booking.

## Where the plugin keeps consent and the cargo type

The consent is the setting `customs_consent`, on the Booking settings page.
`CustomsConsent` reads it. The confirmation says how the whole shop works, so it
is a setting and not a tick on each order. The booking sends `consent` only when
the setting is on, because an absent field is not a refusal but a `false` is.

The cargo type is a select on the booking box of the order screen, in the field
`_bring_nature_of_cargo`. `NatureOfCargo` holds the values and reads the form. A
bulk booking sends no form, so it books `SALE_OF_GOODS`.

The select leaves out `OTHER`, because `OTHER` needs a free text
`natureOfCargo.detail` and the form holds no text field.

## Sources

- [Bring Developer, Customs information](https://developer.bring.com/api/booking/customs/)
- [Bring Developer, Booking API](https://developer.bring.com/api/booking/)
- [Bring Developer, API service portfolio](https://developer.bring.com/api/services/)
