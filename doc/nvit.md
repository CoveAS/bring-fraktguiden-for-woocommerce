# NVIT: Norwegian goods in transit

See [Bring customs data](customs.md) for the `customsInformation` object and the
item line fields that NVIT shares with an export.

NVIT means "norske varer i transitt". A shipment is NVIT when it goes from one
place in Norway to another place in Norway, but passes through Sweden or
Finland. The goods leave Norwegian ground for part of the trip, so they must go
through a customs transit procedure.

Bring carries many domestic shipments this way, because the road through Sweden
or Finland is cheaper and faster.

## Why the rule changed

The EU tightened its data requirements for goods that move inside the EU customs
area. The new system is NCTS 5, and it started in 2025. NVIT goods first had a
temporary exemption, so Bring could send simplified data.

The exemption ended on 31 March 2026. Bring must now give the Norwegian Customs
Authority detailed data for each item line. Bring needs that data from the
sender.

## Dates

| Date | Event |
|---|---|
| 2025 | The EU starts NCTS 5. NVIT keeps a temporary exemption. |
| 31 March 2026 | The exemption ends. The new data requirements apply. |
| 10 April 2026 | Bring adds NVIT support to the Booking API. |
| 1 October 2026 | Bring adds NVIT support to the Mybring web solution. |
| Autumn 2026 | Norwegian Customs may start to stop shipments without the data. |

Bring says no shipment is stopped yet. Bring expects Customs to tighten the
rule after the summer.

## Which shipments the rule covers

The rule follows the postal code, not the sender. It covers a shipment in both
directions between these ranges:

- 0001 to 6999, paired with 8300 to 8599, 9300 to 9499, 9000 to 9159, 9170 to
  9181, and 9188 to 9299.
- 0001 to 7999, paired with 9160 to 9169, 9182 to 9187, and 9500 to 9999.
- 8000 to 9769, paired with 9770 to 9991.

The rule also covers returns. It covers an import that Bring forwards to a
recipient in one of these ranges after customs clearance, because the goods then
leave Norway a second time.

The rule covers all services, with two exceptions. Letters are outside it.
Express services that travel by air are outside it. Bring names no product for
either exception, so the plugin marks the products itself.

### Who does not need to send the data

- A VOEC customer, when Bring reports the VOEC data to the authorities. Bring
  already holds the transit data in that case.
- A business that uses Bring for a per shipment export or import declaration.

A VOEC business that uses another customs agent must send the transit data. A
business that uses a consolidated or bulk declaration must send the transit
data. A business that uses another provider for declarations must send the
transit data.

## The required data per item line

Bring needs four things for each item line, on top of the normal transport
booking:

1. The value and the currency code, including VAT.
2. The tariff number, an HS code, at least 6 digits.
3. A description of the goods.
4. The gross weight and the net weight.

The country of origin is optional. Bring asks for it when the sender has it.

Notes from Bring's own questions and answers:

- The description is free text. There is no required standard.
- The country of origin is the origin of the goods. It is `NO` when the goods
  already cleared Norwegian customs.
- Bring needs the value because it sets the guarantee amount for the transit
  declaration.
- Bring already knows the number of packages from the booking. Some HS codes
  need a second quantity unit, for example litres, pieces or carats.
- Bring waits for a ruling from Customs on consumer to consumer parcels.
- Customs accepts data that is "good enough" for now. The item number must give
  an indication of the goods. The old collective number 54.02.53 says nothing,
  so it has no value.

## Where the rule lives in the plugin

The rule has two parts, and one class asks both.

| Class | Answers |
|---|---|
| `BringFraktguiden\Customs\NvitPostalCodes` | Does the route leave Norway? |
| `BringFraktguiden\Customs\NvitServices` | Does the rule cover the service? |
| `BringFraktguiden\Customs\NvitRule` | Must this booking carry transit data? |

Bring publishes the postal code ranges as prose on its own page and offers no
endpoint and no file for them, so `NvitPostalCodes` holds them.

A service that the rule leaves out carries `'nvit' => false` in
`config/services.php`. A service without the flag is covered.

## Two ways to send the data

Bring gives the sender a choice:

1. Send transit data only for the postal code pairs above.
2. Send transit data for every domestic shipment.

Option 2 sends Bring more personal data than the transit process needs. The
sender stays responsible for that data under the GDPR.

## An NVIT request

Bring's own NVIT example, cut down to the customs part:

```json
{
  "consignments": [
    {
      "correlationId": "INTERNAL",
      "customsInformation": {
        "type": "NVIT",
        "customsDeclarations": [
          {
            "amount": 200,
            "currency": "NOK",
            "customsArticleNumber": "330510",
            "goodsDescription": "Shampoo",
            "grossWeight": 3.2,
            "netWeight": 3,
            "countryCodeOrigin": "CH",
            "numberOfPieces": 2
          },
          {
            "amount": 1000,
            "currency": "NOK",
            "customsArticleNumber": "610910",
            "goodsDescription": "Cotton t-shirts",
            "grossWeight": 2.9,
            "netWeight": 2.5,
            "countryCodeOrigin": "PL",
            "numberOfPieces": 5
          }
        ]
      },
      "packages": []
    }
  ],
  "schemaVersion": 1
}
```

Bring's NVIT examples leave out `consent` and `natureOfCargo`, although the
schema marks both as required. This is not confirmed with Bring. Test a real
booking before you rely on it.

The full examples in Bring's documentation are named "Norwegian Parcels with
NVIT information" and "Nordic Parcels with NVIT information".

## Limits

- The `NVIT` type does not work for an export declaration. Such a shipment needs
  a normal customs declaration.
- Do not send NVIT data when you already send customs data, for example for a
  VOEC shipment.

## Sources

- [Bring, New requirements for Norwegian goods in transit](https://www.bring.no/en/services/customs/norwegian-goods-in-transit-changes)
- [Bring, Nye krav for norske varer i transitt](https://www.bring.no/tjenester/toll/endringer-norske-varer-i-transitt)
- [Bring Developer, Customs information](https://developer.bring.com/api/booking/customs/)
- [Tolletaten, Norske varer i transitt](https://www.toll.no/no/bedrift/transport-og-tollager/norske-varer-i-transitt)
