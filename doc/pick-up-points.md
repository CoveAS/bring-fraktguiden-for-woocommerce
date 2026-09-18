# Pick up points

A pick up point is a shop or a parcel locker that holds a parcel for the
recipient. Bring serves the list of points through the Pickup Point API.

See [Pickup point types](pickup-point-types.md) for the rule that splits manned
points from lockers.

Source: https://developer.bring.com/api/pickup-point/

## The API

The base URL is `https://api.bring.com/pickuppoint`.

Add `.json` to the path for a JSON answer. The API returns XML without it.

### Endpoints

| Path | Returns |
|---|---|
| `/api/pickuppoint/{country}/all` | Every point in the country. |
| `/api/pickuppoint/{country}/id/{id}` | One point. |
| `/api/pickuppoint/{country}/postalCode/{postalCode}` | The points for a postal code. |
| `/api/pickuppoint/{country}/postalCode/{postalCode}/default` | The default point for a postal code. |
| `/api/pickuppoint/{country}/location/{latitude},{longitude}` | The points near a coordinate. |

The country is `NO`, `SE`, `DK` or `FI`. No other country has points.

The answer holds one key, `pickupPoint`. The value is an array of points, even
for a single point lookup.

### Headers

| Header | Value |
|---|---|
| `X-MyBring-API-Uid` | The Mybring login id. |
| `X-MyBring-API-Key` | The Mybring API key. |
| `X-Bring-Client-URL` | The shop URL. |
| `Accept` | `application/json`. |

`WP_Bring_Request` adds all four. See
`classes/common/http/class-wp-bring-request.php`.

### Query parameters

| Parameter | Effect |
|---|---|
| `street` | Sorts by distance from the street. |
| `streetNumber` | Sharpens the street match. |
| `pickupPointType` | Keeps only `manned` or only `locker`. |
| `numberOfResponses` | Limits the count. The default is 10. |
| `searchForText` | Filters on name, address, city, municipality and county. |
| `openingHoursSearchType` | Filters by day, for example `MONDAY` or `ALL_WEEKDAYS`. |
| `openOnOrBefore` | Keeps points open at or before a time, as `HHmm`. |
| `openOnOrAfter` | Keeps points open at or after a time, as `HHmm`. |
| `capabilities` | Keeps points with the named features. |
| `requestCountryCode` | The country of the caller. Norwegian points only. |

### Point fields

The plugin reads these fields.

| Field | Content |
|---|---|
| `id` | The id to send in a booking. |
| `name` | The shop name. |
| `address`, `postalCode`, `city` | The postal address. |
| `latitude`, `longitude` | The position. |
| `openingHours` + a language suffix | The hours as one string. |
| `locationDescription` | The way to the point, in Norwegian. |
| `locationDescriptionEnglish` | The same text in English. |
| `postenMapsLink`, `googleMapsLink` | Map links. |
| `photos` | An array with `bigPhotoUrl` and `smallPhotoUrl`. |
| `additionalServiceCode` | The service code the point needs. |

The API also returns `pickupPointType` (`MANNED` or `LOCKER`), `capabilities`,
`maxParcelDimensions`, `status`, `specialOpeningHourDates` and
`temporaryOpeningHours`.

### Distance

Each point carries `distanceInKm`, `distanceType`, `durationInMinutes` and
`durationType`.

`distanceType` is `DRIVING_DISTANCE` or `AERIAL_DISTANCE`. `durationType` is
`DRIVING_TIME`.

Bring sorts the list by drive time. It falls back to aerial distance when no
road connects the two places, for example across water.

The origin is the address in the request. Without a `street` parameter, Bring
uses the centre of the postal code area. Bring advises to send the address.

`PickUpPointData` keeps `distanceInKm` only. The modal prints it under the
address. A point without a distance prints nothing.

The opening hours arrive as one string per language, such as
`openingHoursNorwegian` and `openingHoursEnglish`. The string is for a reader.
It is not machine readable.

`maxParcelDimensions` holds `length`, `width` and `height`. A Pakkeboks locker
in Norway carries this object. A parcel larger than the box does not fit.

### Limits

Bring throttles a client above 80 requests per second. The answer is then status
429.

Bring asks a client that stores points to refresh them every 24 hours. A point
moves, opens or closes at any time.

## How the plugin uses the API

`pro/pickuppoint/` holds the pick up point code. It is a Pro feature.

### The lookup

`GetRawPickupPointsAction` calls the `postalCode` endpoint. It passes the
shipping country and the shipping postal code of the customer.

It adds `street` from the shipping address, so Bring sorts the list by distance.

It adds `pickupPointType` when the caller names a type, or when
`PickupPointType::fetch_type()` names one. An empty type sends no filter, and
Bring returns both kinds.

The filter `bring_fraktguiden_get_pickup_points_args` changes the query
parameters. Use it to raise `numberOfResponses` or to add a capability.

The action returns an empty array on any error. The checkout then shows no
point.

### The value object

`PickUpPointData::fromRaw()` maps a raw point to a typed object. It drops a
point without an `id` or a `name`.

It lowercases `pickupPointType`, so the object holds `manned` or `locker`.

It picks the opening hours and the location description for the site locale. A
locale outside NO, DK, SE and FI gets the English text.

### The checkout

`PickUpPoint::register_javascript()` runs the lookup on page load. It puts the
points in `window._fraktguiden_data.pick_up_points`.

It also puts the chosen point per type in `selected_pick_up_points`, and the
type of each rate in `pick_up_point_rate_types`.

`pro/assets/js/pick-up-point-checkout.js` draws the picker and the modal. The
modal is a custom element named `pick-up-points-modal` with a shadow root. The
picker is a plain `div` with the class `bring-fraktguiden-pick-up-point-picker`.

The browser filters the one list per rate, in `utility.pointsForType()`. A rate
of type `locker` therefore shows lockers only.

The customer opens the modal, picks a point, and the script posts the id and the
type to the AJAX action `bfg_select_pick_up_point`. The action stores the id in
the session, under the key that `PickupPointType::session_key()` returns.

The AJAX action `bfg_get_pick_up_points` reloads the list after the customer
changes the address.

A shop can pick the legacy picker instead, with the setting **Style**. The
legacy picker is a select field. `LegacyPickupPoints` holds it.

### The shipping key

The shipping key names every part the lookup depends on: the country, the
postal code and the street. `CustomerAddress::getKey()` builds it in PHP, and
`utility.getShippingKey()` builds the same value in the browser.

The block checkout reloads the points only when the key changes. The street
belongs in the key because Bring sorts the points by the distance from it. A new
street on the same postal code gives a new order.

The classic checkout ignores the key and reloads on every `updated_checkout`.

### Which service shows a picker

A service shows a picker when its `pickup_point_cb` setting holds a value. The
setting sits per service in the plugin settings, not in `config/services.php`.

`PickUpPoint::supports_pick_up_point()` reads that setting.

`PickupPointType::for_service()` gives the type the service offers.

### The order

`PickUpPoint::attach_item_meta()` saves the id on the shipping line as
`pickup_point_id`. It reads the session key for the type of the service, so a
locker rate saves the locker the customer picked.

The old meta keys `_fraktguiden_pickup_point_id`,
`_fraktguiden_pickup_point_postcode` and `_fraktguiden_pickup_point_info_cached`
stay for orders from an older version.

The order screen looks the point up again by id, through the `id` endpoint. See
`pro/order/class-bring-wc-order-adapter.php`.

A saved id can disappear before the booking. Bring then refuses the booking. The
shop owner must pick a new point on the order screen.
