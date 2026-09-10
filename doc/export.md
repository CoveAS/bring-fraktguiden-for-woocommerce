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

The plugin builds only `sender` and `recipient` in `parties`. See
[class-bring-booking-consignment-request.php:229-236](../pro/booking/consignment-request/class-bring-booking-consignment-request.php#L229-L236).

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

## Sources

- [Bring Developer, Customs information](https://developer.bring.com/api/booking/customs/)
- [Bring Developer, Booking API](https://developer.bring.com/api/booking/)
- [Bring Developer, API service portfolio](https://developer.bring.com/api/services/)
