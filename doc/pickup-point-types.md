# Pickup point types

Bring sells two ways to collect a parcel. A manned pickup point is a shop or a
post office. A Pakkeboks is an unmanned parcel locker, open all day.

## What the API gives

The Pickup Point API returns every point near a postal code. Each point carries
a `pickupPointType` field, either `MANNED` or `LOCKER`. It also carries a `type`
field with a country code number. Type 37 is a Pakkeboks in Norway.

The request takes a `pickupPointType` parameter with the value `manned` or
`locker`. Without the parameter the answer holds both kinds.

A Pakkeboks point also carries `maxParcelDimensions`. A parcel larger than the
locker fails at booking time.

Source: https://developer.bring.com/api/pickup-point/

## The services

Service 5800 Pakke til hentested delivers to any pickup point. Service 5801
Pakkeboks delivers to a locker only. Both are Norwegian domestic services for a
private recipient.

A Pakkeboks parcel weighs at most 10 kg and measures at most 60 x 50 x 44 cm.

Source: https://developer.bring.com/api/services/ and
https://www.bring.no/tjenester/pakker-og-gods/private-nasjonalt/pakkeboks

## The rule in this plugin

A service names the type it offers in `config/services.php`, under the key
`pickuppoint_type`. Pakkeboks carries `locker`.

A locker service claims every locker point. While a locker service is on, a
pickup point service without a type of its own offers manned points only. The
customer therefore never sees the same locker under two services at two prices.

With no locker service on, the shop setting `pickup_point_types` decides. The
setting never overrules a service that names its own type.

`BringFraktguidenPro\PickUpPoint\PickupPointType` holds the rule.

## One chosen point per type

The customer chooses one point per type, not one per rate. The session holds the
choice under `bring_fraktguiden_pick_up_point` for a manned point and under
`bring_fraktguiden_pick_up_point_locker` for a locker.

The checkout fetches one list of points and the browser filters it per rate. A
rate carries its type in the `pick_up_point_rate_types` map.
